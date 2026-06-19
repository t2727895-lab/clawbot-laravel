<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClawbotService
{
    /**
     * Extract plain text from HTML content
     */
    private function extractPlainText(string $html): string
    {
        // Remove script and style elements
        $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $html);
        
        // Convert HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Replace <br>, <br/>, <br /> with newlines
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        
        // Replace </p>, </div>, </h1>-</h6> with newlines
        $text = preg_replace('/<\/(p|div|h[1-6]|li)>/i', "\n", $text);
        
        // Replace </li> with newline
        $text = preg_replace('/<\/li>/i', "\n", $text);
        
        // Remove all HTML tags
        $text = strip_tags($text);
        
        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Replace multiple consecutive newlines with double newlines
        $text = preg_replace("/\n\n+/", "\n\n", $text);
        
        // Trim whitespace from each line
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, function($line) { return $line !== ''; });
        $text = implode("\n", $lines);
        
        // Trim leading and trailing whitespace
        $text = trim($text);
        
        return $text;
    }

    /**
     * Publish a post to Clawbot
     */
    public function publish(Post $post): array
    {
        // Increase execution time limit for this operation
        set_time_limit(500); // 5 minutes

        try {
            $baseUrl = config('services.clawbot.base_url');
            $apiKey = config('services.clawbot.api_key');

            if (!$baseUrl || !$apiKey) {
                Log::warning('Clawbot credentials not configured', [
                    'post_id' => $post->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'Clawbot credentials not configured',
                    'code' => 'MISSING_CREDENTIALS',
                ];
            }

            // Call OpenClaw chat completion with LinkedIn skill
            $url = $baseUrl . '/v1/chat/completions';

            // Extract plain text from HTML content
            $plainTextContent = $this->extractPlainText($post->content);

            // Build the LinkedIn posting prompt with post content and image
           $linkedinPrompt = "Please post this content on LinkedIn using your LinkedIn skill.
            The content and image have already been approved by the admin, so do not ask for approval. Post it directly.
            Content:
            \"{$plainTextContent}\"
            Image:
            \"{$post->image}\"
            Please publish it now.";

            $data = array(
                'model' => 'openclaw',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $linkedinPrompt
                    )
                ),
                'stream' => false
            );

            $payload = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 second timeout
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ));

            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);

            // Log the result
            Log::info('LinkedIn posting via Clawbot', [
                'post_id' => $post->id,
                'status_code' => $http_code,
                'response' => $responseData
            ]);

            // Create PostLog entry
            $success = $http_code === 200 && !$curl_error;
            PostLog::create([
                'post_id' => $post->id,
                'action' => 'publish_attempt',
                'status' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Posted to LinkedIn via Clawbot successfully'
                    : ($curl_error ?: 'Clawbot returned an error'),
                'response_data' => $responseData,
            ]);

            if ($success) {
                // Update post status to published if not already published
                if ($post->status !== 'published') {
                    $post->update([
                        'status' => 'published',
                        'published_at' => now(),
                    ]);
                }

                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/webhook.log'),
                ])->info('LinkedIn Post Published Successfully', [
                    'post_id' => $post->id,
                    'status' => $post->status,
                    'action' => 'publish',
                    'timestamp' => now()->toDateTimeString(),
                    'clawbot_response_code' => $http_code,
                    'clawbot_response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'Post published to LinkedIn successfully',
                    'response' => $responseData,
                ];
            } else {
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/webhook.log'),
                ])->warning('LinkedIn Post Failed', [
                    'post_id' => $post->id,
                    'status' => $post->status,
                    'action' => 'publish',
                    'timestamp' => now()->toDateTimeString(),
                    'http_code' => $http_code,
                    'curl_error' => $curl_error,
                    'clawbot_response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $curl_error ?: ($responseData['error'] ?? 'Clawbot returned an error'),
                    'response' => $responseData,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Clawbot publish failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            PostLog::create([
                'post_id' => $post->id,
                'action' => 'publish_attempt',
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'EXCEPTION',
            ];
        }
    }
}
