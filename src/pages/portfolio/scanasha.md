---
layout: "../../layouts/PostLayout.astro"
title: "scanasha"
description: "defi protocol review automation project using permission scanning, data aggregation & AI"
pubDate: "2025-10-25"
icon: "/projects/scanasha/icon.svg"
status: "prototype"
source: "https://github.com/in-th3-l00p/scanasha"
article: "https://www.cherryservers.com/blog/supporting-innovation-eth-bucharest-hackathon"
priority: 0
---

created @ [ETH Bucharest 2025](https://ethbucharest.ro/) alongside [@Marc__Vlad](https://x.com/Marc__Vlad), [Emilien Duc](https://www.linkedin.com/in/emilien-duc-561b38194/) & [Yves Boutellier](https://www.linkedin.com/in/yvesboutellier/)

<iframe src="https://www.youtube.com/embed/kOa7W_3nCWU" height="400px"></iframe>

My hackathon teammates are part of [defiscan](https://www.defiscan.info/), an organzation that provides *"comprehensive analysis and ratings of DeFi protocols"*.
Yves created a protocol [**permission scanner**](https://github.com/deficollective/permission-scanner), that returns *"a list of the smart contracts, the identified permissioned functions and respective permission owners"*. This technology is used for writing reports about **how decentralized** DeFi protocols are.

Still, the creation of reports requires a significant amount of manual work. The results of the permission scanner have to be interpreted in *human-readable language*, and charts according to the data have to be created. All of these tasks sound so exhausting ever since AI came up lol.

**Scanasha** is software that automatically creates such a report. It aggregates data about a protocol and outputs the level of decentralization it has. It encourages people to contribute to researching decentralization levels on protocols, by giving out bounties.

## technology
Everything is accessed through a frontend built on [**Akasha**](https://akasha.org/world/), a modular platform for apps. The permission scanner is written in Python, accessed through a REST api running on Flask. The aggregation of data works using OpenAI's GPT. With the [**VIALabs**](https://vialabs.io/) oracle, we allow contributors to claim a bounty for eligible contributions. The oracle reports on-chain whether a given PR is open, merged, or closed without merge.

![screenshot of how protocol's contracts are indexed](/projects/scanasha/screenshot1.png)
![screenshot of how a generated report looks like](/projects/scanasha/screenshot2.png)