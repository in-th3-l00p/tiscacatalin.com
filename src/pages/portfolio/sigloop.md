---
layout: "../../layouts/PostLayout.astro"
title: "sigloop"
description: "smart contract wallets for AI agents with safety controls & automated payment handling"
pubDate: "2025-11-15"
icon: "/projects/sigloop/icon.svg"
status: "development"
live: "https://sigloop.tiscacatalin.com"
source: "https://github.com/in-th3-l00p/sigloop"
priority: 0
---

**sigloop** provides AI agents with smart contract wallets featuring safety controls and automated payment handling. built on ERC-4337 (account abstraction), it enables agents to operate with controlled spending limits and permission scoping.

## key features

* **session keys** — time-bound, contract-scoped permissions with instant revocation
* **x402 payment automation** — auto-detect 402 responses, validate budgets, sign, and retry
* **passkey onboarding** — account creation without seed phrases
* **budget guardrails** — per-agent spending limits, token allowlists, and real-time audit logs enforced on-chain
* **framework agnostic** — works with any agent framework, runtime, or deployment target

## tech stack

* ERC-4337 account abstraction
* ZeroDev integration
* smart contract wallets
* passkey authentication
* pre-built modules for recovery, chain abstraction, and DeFi
