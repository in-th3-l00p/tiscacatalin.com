
---
layout: "../../layouts/PostLayout.astro"
title: "ai dev progress app"
description: "journaling my daily AI learning process, mainly deep learning"
pubDate: "2025-08-31"
icon: "/projects/ai-progress/icon.png"
technologies: ["typescript", "supabase", "lovable"]
status: "completed"
live: "https://ai-progress.tiscacatalin.com/"
source: "https://github.com/in-th3-l00p/deep-learn-diary"
---

*"learning AI can get messy, so I built myself a streak-based journal to keep me accountable"*

**loopjournal** is a private web app that lets me log my daily AI progress and keeps track of a learning streak.  
each day I can write exactly one entry, so everything is just a habit-building tool that shows my consistency.

## features
* 🔐 supabase auth (magic links) locked to my account, no public registration.
* ✍️ one journal entry per day (edit or overwrite).
* 🔥 streak counter that resets if I skip.
* 📜 infinite scrolling list of past notes.
