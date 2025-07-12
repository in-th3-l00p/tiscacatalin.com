---
layout: "../../layouts/PostLayout.astro"
title: "docloop"
description: "platform for creating PDFs, completely from client-side using WASM"
pubDate: "2025-07-12"
icon: "/projects/docloop/docloop.png"
technologies: ["rust", "wasm", "react", "tailwind"]
status: "completed"
github: "https://github.com/username/ai-chat-assistant"
live: "https://docloop.tiscacatalin.com"
source: "https://github.com/in-th3-l00p/docloop"
---

*it had happened so many times for me to search online for tools such as an image to pdf convertor, and all I got
were tools that were asking for dollars instead of doing what're they supposed to do*

## introducing *docloop*
a simple tool that creates PDFs. just that, no ads, no payment required. 

it is based on a React frontend, that uses a WASM binary to create the PDF file. For WebAssembly, I used Rust, and
the [printpdf](https://docs.rs/printpdf/latest/printpdf/) library.

## further direction
the philosophy of this project was providing free, easy to use tools online, looking forward into maintaining this
mindset for future projects

simple as that, enjoy