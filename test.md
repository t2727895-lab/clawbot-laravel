Got it! The problem is clear: **the agent is guessing or constructing the image URL instead of strictly using the `previewUrl` returned by the `image-gen` command.**

We need to force the agent to **parse the JSON output** of `linkedin-cli image-gen`, extract the exact `previewUrl` field, and pass **that exact string** to the `post-draft-cli` script. No constructing, no hardcoding—just pass what the command gives back.

Here are the **exact paragraphs** to update in your `agent.md`. Replace the **current "Workflow A" section** (from Step 1 to Step 8) with this updated version:

---

### Replace This Entire Block (Workflow A)

**Find this in your current file:**
```markdown
## Workflow A: Automated Scheduled Posts (Default)
...
(All the way down to Step 8)
```

**Replace it with this:**

---

```markdown
## Workflow A: Automated Scheduled Posts (Default)

*Use this when the user asks to schedule posts (e.g., "post 3 times a week").*

1.  **Plan**: Determine the posting dates and assign topics using the Rotation Logic.
2.  **Research**: Find trending news/statistics for the specific pillar.
3.  **Write**: Draft the post following the "Problem/Solution Formula".
4.  **Generate Image & Capture Preview URL (CRITICAL)**:
    - Run: `linkedin-cli image-gen --prompt "Your detailed prompt"`
    - **The command returns a JSON object**. Example response:
      ```json
      {
        "path": "/var/www/html/images/generated_...png",
        "previewUrl": "http://192.168.56.10/images/generated_...png",
        "filename": "generated_...png",
        "size": 1234567
      }
      ```
    - **🚨 RULE:** You **MUST** extract the exact `previewUrl` from this JSON output. 
    - **DO NOT** construct the URL yourself (e.g., do not use `http://10.0.2.2` or `https://cdn.eiglou.com` unless it is exactly the `previewUrl` returned). The command provides the correct public URL—use it as-is.
5.  **Preview**: Show the user the final text + the generated image preview (use the `previewUrl` you just captured).
6.  **Get Confirmation**: Ask the user: *"Ready to save this draft to the database for automatic posting?"* Wait for explicit "Yes".
7.  **Save to Database (Draft) - USING THE EXACT PREVIEW URL**:
    - Read the skill: `/home/vagrant/.openclaw/workspace/skills/post-draft-cli/SKILL.md`
    - Run the script with the absolute path:
      ```bash
      node /home/vagrant/.openclaw/workspace/skills/post-draft-cli/index.js "<Post Content>" "<EXACT_PREVIEW_URL_FROM_STEP_4>" "<Scheduled DateTime>"
      ```
    - **🚨 CRITICAL**: The second argument (image_url) **MUST** be the exact `previewUrl` string you captured in Step 4. 
      - ✅ Correct: `"http://192.168.56.10/images/generated_2024-01-15T10-30-45.png"`
      - ❌ Wrong: Any other URL, empty string, or a URL you guessed/constructed.
8.  **Report**: Inform the user that the draft is saved. (Note: The agent does *not* publish to LinkedIn directly here. The Laravel backend cron job will handle the final publishing at the scheduled time).
```

---

### Why this fixes your database issue:

1. **Forces extraction**: The agent is explicitly told to read the JSON and grab `previewUrl`.
2. **Bans guesswork**: It is strictly forbidden to construct a URL or use a default/base URL.
3. **Consistency**: Regardless of whether your server returns `http://10.0.2.2` or `https://cdn.eiglou.com`, the agent will pass the exact correct string to the database, ensuring your `image_url` column is never empty or mismatched.

---

### One more small addition (Optional but recommended):

Add this **Global Rule** at the very top of your `agent.md` (under "Role & Mission") so the agent never forgets:

```markdown
## 🚨 Global Rule: Image URL Handling

Whenever you run `linkedin-cli image-gen`, the command returns a JSON object containing a `previewUrl`. 
**You must always use this exact `previewUrl`** for anything that requires an image link (drafting, previewing, or posting). 
Never build, guess, or replace the image URL with a different domain/IP unless the user explicitly tells you to do so.
```

---

Just paste the updated **Workflow A** over the old one, and optionally add the Global Rule at the top. Your agent will now save the correct, fully qualified image URLs to your Laravel database every single time.