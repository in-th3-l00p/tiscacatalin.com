---
layout: "../../layouts/PostLayout.astro"
title: "cchat"
description: "I/O multiplexing in C, through a CLI chat app from scratch, meant for showcasing sockets"
pubDate: "2025-10-01"
icon: "/projects/cchat/icon.svg"
technologies: ["c", "posix sockets", "select(2)", "getline(3)", "make"]
status: "completed"
priority: 2
article: "https://tiscacatalin.com/posts/cchat"
medium: "https://medium.com/@inth3l00p/the-c-select-function-the-internet-58f6068d3cfd"
source: "https://github.com/in-th3-l00p/cchat"
---

*a tiny chat that demonstrates I/O multiplexing with `select(2)`, showing the essential moving parts of low‑level networking without magic.*

## general info
- **tech**: C, POSIX sockets, `select(2)`, `getline(3)`, simple Makefiles
- **protocol**: length‑prefixed frames (4‑byte big‑endian) up to 64 KiB
- **shape**: one single‑process server handling multiple clients; one client that multiplexes stdin and the socket (no threads)
- **goal**: clarity—highlight the core ideas behind multiplexed I/O and framing

## why it matters
I/O multiplexing lets one process wait on many file descriptors at once. No threads, no async frameworks—just `select(2)` and clear state machines.

## architecture
### server
- listens on TCP
- tracks connected clients in an indexable container keyed by socket fd (keeps `max_fd` for efficient scans)
- uses `select(2)` to wake up for new connections and readable clients
- reads messages incrementally: 4‑byte length header → payload buffer
- first complete message from a client sets its nickname
- subsequent messages are broadcast as `nickname: message` to all clients

### client
- connects via `getaddrinfo(3)` (defaults to `127.0.0.1:8080`)
- uses `select(2)` to multiplex stdin and the socket
- frames outbound messages with a 4‑byte big‑endian length
- prints complete inbound frames to stdout
- the first line you send sets your nickname

## framing protocol
- `uint32_t length_be` (network byte order) + `length` bytes of payload
- max message size: `65536` bytes (`MAX_MESSAGE_LENGTH`)
- server treats the client’s first message as the nickname (max 255 chars)

## execution flow
### server
1. accept new connections when the listening socket is readable
2. for each readable client socket, progress its receive state machine:
   - read header until 4 bytes → decode length → ensure buffer
   - read payload until `length` bytes
   - on full frame: set nickname or broadcast

### client
1. wait on both stdin and the socket using `select(2)`
2. if stdin is ready: `getline` → strip newline → frame and send
3. if the socket is ready: incrementally read header and payload; on full frame, print to stdout

## build and run
requirements: POSIX system with `gcc` or clang.

```bash
cd server && make
cd ../client && make

# run the server
./server/bin/server

# run a client (defaults to 127.0.0.1:8080)
./client/bin/client
# or
./client/bin/client -h 127.0.0.1 -p 8080
```

## notes
- incremental reads handle partial headers/payloads robustly
- no threads: one process, one `select(2)` loop on each side
- natural extensions: auth, rooms, and swapping `select(2)` for `poll(2)`/`epoll(7)`/`kqueue(2)`


