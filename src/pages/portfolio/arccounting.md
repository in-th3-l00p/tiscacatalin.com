---
layout: "../../layouts/PostLayout.astro"
title: "arccounting"
description: "all-in-one dashboard for private payroll, invoicing, and AI business intelligence on Arc network"
pubDate: "2026-04-05"
icon: "/projects/arccounting/icon.svg"
status: "development"
live: "https://www.arc-counting.xyz/"
source: "https://github.com/Cannes2026EthGlobal/webapp"
priority: 1
---

**arccounting** is a business payments platform built at ETHGlobal Cannes 2026. it enables operators to manage employee payroll, freelancer payments, and customer invoicing through a unified interface — all powered by smart contracts on the Arc network.

## presentation

<video controls preload="metadata" poster="/projects/arccounting/screenshots/1.png" style="width:100%; border-radius:8px; margin-bottom:1rem;">
  <source src="/projects/arccounting/demo.mp4" type="video/mp4" />
</video>

## screenshots

<div class="screenshot-gallery">
  <img src="/projects/arccounting/screenshots/1.png" alt="operator dashboard overview" onclick="openLightbox(0)" />
  <img src="/projects/arccounting/screenshots/2.png" alt="financial overview & recent activity" onclick="openLightbox(1)" />
  <img src="/projects/arccounting/screenshots/3.png" alt="AI insights & analysis tools" onclick="openLightbox(2)" />
  <img src="/projects/arccounting/screenshots/4.png" alt="Arc AI assistant chat" onclick="openLightbox(3)" />
  <img src="/projects/arccounting/screenshots/5.png" alt="customers & receivables" onclick="openLightbox(4)" />
</div>

<dialog id="screenshot-lightbox" class="screenshot-dialog" onclick="if(event.target===this)this.close()">
  <div class="dialog-content">
    <button class="dialog-close" onclick="this.closest('dialog').close()">&times;</button>
    <button class="dialog-nav prev" onclick="navLightbox(-1)">&#8249;</button>
    <img id="lightbox-img" src="" alt="" />
    <button class="dialog-nav next" onclick="navLightbox(1)">&#8250;</button>
  </div>
</dialog>

<script>
  const lbImages = [
    "/projects/arccounting/screenshots/1.png",
    "/projects/arccounting/screenshots/2.png",
    "/projects/arccounting/screenshots/3.png",
    "/projects/arccounting/screenshots/4.png",
    "/projects/arccounting/screenshots/5.png"
  ];
  let lbIndex = 0;

  function openLightbox(i) {
    lbIndex = i;
    const img = document.getElementById("lightbox-img");
    img.src = lbImages[lbIndex];
    document.getElementById("screenshot-lightbox").showModal();
  }

  function navLightbox(dir) {
    lbIndex = (lbIndex + dir + lbImages.length) % lbImages.length;
    document.getElementById("lightbox-img").src = lbImages[lbIndex];
  }

  document.addEventListener("keydown", (e) => {
    const dialog = document.getElementById("screenshot-lightbox");
    if (!dialog.open) return;
    if (e.key === "ArrowLeft") navLightbox(-1);
    if (e.key === "ArrowRight") navLightbox(1);
    if (e.key === "Escape") dialog.close();
  });
</script>

## key features

* **employee payroll** — manage salaries and advance payments via smart contracts
* **freelancer payments** — track and pay freelancers in USDC on Arc network
* **customer billing** — usage-based billing, B2B invoicing, and one-time product payments
* **walletconnect pay** — generate checkout links for seamless crypto payments
* **real-time treasury dashboard** — monitor balances, obligations, and cash flow at a glance
* **AI business intelligence** — AI agent for querying and analyzing business data

## tech stack

built with Next.js, Convex DB, Reown Auth, WalletConnect Pay, Chainlink CRE, and Arc smart contracts.
