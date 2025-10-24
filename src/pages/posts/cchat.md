---
layout: "../../layouts/PostLayout.astro"
title: "the C select function & the internet"
description: "an introduction to network programming \\w a chat app"
pubDate: "2025-10-12"
---

Network programming is the best and worst field that you can put your hands on when learning a programming language, or programming in general. This article will demonstrate how to enable multiple programs to communicate with each other at a fundamental level.

I’ll showcase the process of building a single-threaded messaging app in C using the **POSIX sockets API, **the standard for Linux and macOS. It is portable across Unix-like systems, and Windows offers a similar interface.

The app has two parts:
* **server** -> accepts connections and forwards messages
* **client** -> that sends what you type and displays what others send

The first step is initializing the server & making it accept and handle connections.

## project’s structure

The most important concept of network programming is **sockets**. A **socket** is a communication channel that lets two programs send messages to each other, locally or over the internet: simple as that, period.

In our app, the server opens a **socket** to listen, and each client opens one to connect. Messages go in through a client’s **socket**, reach the server, and get sent out through all the others.

The project structure will be based on two binaries: client & server. Both will have their own directories & *Makefiles*. This is how the project’s directory is structured:

```text
    cchat
    |-- client
        |   |-- Makefile
        |   |-- bin
        |   |   |-- client
        |   |   |-- server
        |   |-- src
        |       |-- client.c
        |       |-- client.h
        |       |-- config.h
        |       |-- main.c
        |       |-- processing.c
        |       |-- processing.h
        |-- server
            |-- Makefile
            |-- bin
            |   |-- server
            |-- src
                |-- client_processing.c
                |-- client_processing.h
                |-- clients.c
                |-- clients.h
                |-- config.h
                |-- main.c
                |-- processing.c
                |-- processing.h
                |-- server.c
                |-- server.h
```

And this is how the server’s *Makefile* looks (similar to the client one):
```makefile
CC = gcc
CFLAGS = -Wall -Wextra -O2
SRC = src
BIN = bin
TARGET = server
SRCS = $(wildcard $(SRC)/*.c)

.PHONY: all clean

all: $(BIN)/$(TARGET)

$(BIN)/$(TARGET): $(SRCS) | $(BIN)
    $(CC) $(CFLAGS) -o $@ $(SRCS)

$(BIN):
    mkdir -p $(BIN)

clean:
    rm -rf $(BIN)
```

## building the server

First, let’s understand how to build the **server.**

The *entrypoint* of the binary is found in the server/src/main.c (the *main function* ofc). The second most important structure of the server can be found within the server.h and server.c files, where the Server structure is implemented, which has the purpose of representing the entire state of the program:

```c
typedef struct {
    int sock; // server's socket fd
    Clients clients; // data structure that stores the connected clients
} Server;

// ofc we're providing an abstraction through methods for using this
Server create_server();
void destroy_server(Server* server);
void run_server(Server* server);
```

It has an int called sock which is used for identifying the server’s socket. This variable is called a *file descriptor*, and all you have to know is that the actual socket is managed by the OS; our only job is keeping track of this number.

### server’s socket

The next task is getting the socket created, and that’s possible using the socket function:

```c
// signature extracted from its manual page
int socket(int domain, int type, int protocol)
```

Understanding its parameters is a topic for another article; however, our concern is to configure it so that it will be available for accepting new connections on a protocol that will keep the connection alive (for your interest, we’re making use of the TCP protocol through our server’s socket):

```c
// creating the socket
int yes = 1;
if ((server.sock = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
    perror("create_server: socket failed");
    exit(1);
}

// configure it so that it can use the same address after being destroyed
setsockopt(server.sock, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(yes));

// creating the address that identifies the socket on the internet
struct sockaddr_in sa;
memset(&sa,0,sizeof(sa));
sa.sin_family = AF_INET;
sa.sin_port = htons(PORT);
sa.sin_addr.s_addr = htonl(INADDR_ANY);

// bind the created address & start listening for connections
if (
    bind(server.sock, (struct sockaddr*)&sa, sizeof(sa)) == -1 ||
    listen(server.sock, BACKLOG) == -1
) {
    close(server.sock);
    perror("create_server: bind or listen failed");
    exit(1);
}
```

In the snippet submitted above you can also see the process of binding the socket to the address (that identifies it on the internet) and it triggers it to start listening for new connections.

### accepting clients

So, we’re listening for new connections, but a structure that stores the connected users is required. Its implementation can be found within the clients.h and clients.c files:

```c
typedef struct Client {
    int sock;
    char name[MAX_NAME_LENGTH];
    uint8_t header_buf[4];
    uint32_t header_bytes_read;
    uint8_t* payload_buf;
    uint32_t payload_capacity;
    uint32_t payload_length;
    uint32_t payload_bytes_read;
} Client;

// key -> value store of the connected
// key is the socket fd
typedef struct Clients {
    Client* connected[MAX_CLIENTS];
    uint32_t count;
    int max_fd; // highest used  socket fd
} Clients;
```

Now, this chat application is not about showcasing fundamental algorithms; *not a single proper key -> value DSA was used*. However, as a proof of concept, storing the clients using an array indexed by the client’s socket is enough.

An interesting particularity of this DSA is the max_fd field. It simply stores the maximum client socket identified. The reason we’re doing this is to utilize the select function that gives us the ability to handle multiple connections at a time; however, this will be showcased later.

### running & processing

The server runs inside a function named process_server, which is repeatedly invoked within an infinite loop:

```c
void run_server(Server* server) {
    printf("run_server: running server on port %d\n", PORT);
    while (1) 
        process_server(server);
}

void process_server(Server* server) {
    fd_set read_fds = get_read_fds(server);
    process_server_socket(&read_fds, server);
    process_clients_socket(&read_fds, server);
}
```

The function is split into two other processing functions: one that processes the server socket (accepting new connections) and another that receives and sends messages to the clients. But the most interesting aspect of this part is the get_read_fds function.

This function’s purpose is to determine which of the server’s sockets are ready to be read. This is the multiplexing part of the project: instead of blocking on a single socket, the program checks all of them and processes whichever are readable at that moment.

Let’s take a look at its implementation:

```c
static inline fd_set get_read_fds(Server* server) {
    fd_set read_fds;
    FD_ZERO(&read_fds);
    FD_SET(server->sock, &read_fds);
    for (int i = 0; i < MAX_CLIENTS; i++) {
        if (server->clients.connected[i] != NULL) {
            FD_SET(server->clients.connected[i]->sock, &read_fds);
        }
    }
    int max_fd = server->clients.max_fd;
    if (server->clients.max_fd < 0)
        max_fd = server->sock;
    max_fd++;

    struct timeval timeout = {0};
    timeout.tv_sec = 1;
    timeout.tv_usec = 0;

    int activity = select(
        max_fd, 
        &read_fds, 
        NULL, 
        NULL, 
        &timeout
    );

    if (activity < 0)
        perror("get_read_fds: select failed");
    return read_fds;
}
```

First, there’s this data type called fd_set . It is a **bitmask** used by select to track file descriptors. Each bit represents one descriptor: set bits mark those being monitored, and after select returns, only the bits for ready descriptors remain set.

The select function is the core of our **multiplexing**. It takes an fd_set as input and marks which *FDs* are ready for reading. It also requires the highest *fd* value plus one, stored in the server structure. The optional timeout controls how long it waits. If omitted, select blocks until at least one *fd* becomes readable.

Server processing only handles new connections. If the server socket is marked readable by select, it means a client is waiting, so calling accept won’t block.

```c
static inline void process_server_socket(
    fd_set* read_fds, 
    Server* server
) {
    if (!FD_ISSET(server->sock, read_fds))
        return;
    struct sockaddr_in sock;
    socklen_t sock_len = sizeof(sock);
    int sock_fd = accept(server->sock, (struct sockaddr*)&sock, &sock_len);
    if (sock_fd == -1) {
        perror("process_server: accept failed");
        return;
    }
    printf("process_server: accepted connection from %s\n", inet_ntoa(sock.sin_addr));
    add_client(&server->clients, sock_fd, "");
}
```

The accept function also provides information about the client’s address, through the sockaddr_in structure. Afterwards, the add_client function adds the new client to our server structure.

And of course, the function that is processing the *clients* also makes use of the fd_set gathered from the get_read_fds function:

```c
static inline void process_clients_socket(
    fd_set* read_fds, 
    Server* server
) {
    for (int i = 0; i <= server->clients.max_fd; i++) {
        if (server->clients.connected[i] != NULL) {
            if (FD_ISSET(server->clients.connected[i]->sock, read_fds)) {
                process_client(server, i);
            }
        }
    }
}
```

### chat protocol

It is time to talk about the protocol for sending messages. All the logic of it is found within the process_client function:

```c
void process_client(
    Server* server, 
    int client_index
) {
    Client* client = server->clients.connected[client_index];

    // first part of the protocol: getting the length of the message
    RecvStatus s = read_header_and_prepare_payload(client);
    if (handle_recv_status(
        "process_client: header read/prepare failed",
        s,
        server,
        client
    ))
        return;

    // reading the message
    s = read_payload(client);
    if (handle_recv_status(
        "process_client: payload read failed",
        s,
        server,
        client
    )) 
        return;

    // sending the message to everybody
    if (
        client->payload_length > 0 && 
        client->payload_bytes_read == client->payload_length
    ) {
        handle_complete_message(
            server,
            client
        );
        reset_receive_state(client);
    }
}
```

The protocol is simple; first, the client sends the **length** of the upcoming message through an **unsigned 32-bit integer**. This happens within the read_header_and_prepare_payload , that makes use of the POSIX recv function that reads bytes sent by the client through its *socket*:

```c
while (client->header_bytes_read < 4) {
    ssize_t n = recv(
        client->sock,
        client->header_buf + client->header_bytes_read,
        4 - client->header_bytes_read,
        MSG_DONTWAIT
    );
    if (n > 0) {
        client->header_bytes_read += (uint32_t)n;
        continue;
    } else if (n == 0) {
        return RECV_CLOSED;
    } else {
        if (errno == EAGAIN || errno == EWOULDBLOCK)
            return RECV_WOULDBLOCK;
        return RECV_ERROR;
    }
}
```

The RecvStatus is nothing special, just a way of classifying errors:

```c
typedef enum RecvStatus {
    RECV_OK = 0,
    RECV_WOULDBLOCK = 1,
    RECV_CLOSED = 2,
    RECV_ERROR = 3
} RecvStatus;
```

After reading the required bytes for an uint32_t we’re **processing it**. That means handling the byte order.

**Endianness** is the order in which a system stores the bytes of a multi-byte value.

* **big endian** means the most significant byte (the “big end”) is stored first. For example, the number 0x12345678 is stored in memory as:12 34 56 78

* **little endian**, used by most modern CPUs (like x86), stores the least significant byte first: 78 56 34 12

Big endianness is always used for sending numbers over the internet. To ensure portability across architectures, the POSIX API provides helper functions like ntohl , which is used for the data received that represents the length of the upcoming message.

Reading the message is simple, as we already know how many bytes we have to read from the *client’s socket*:

```c
static inline RecvStatus read_payload(Client* client) {
    if (client->header_bytes_read < 4 || client->payload_length == 0)
        return RECV_OK;

    while (client->payload_bytes_read < client->payload_length) {
        ssize_t n = recv(
            client->sock,
            client->payload_buf + client->payload_bytes_read,
            client->payload_length - client->payload_bytes_read,
            MSG_DONTWAIT
        );
        if (n > 0) {
            client->payload_bytes_read += (uint32_t)n;
            continue;
        } else if (n == 0) {
            return RECV_CLOSED;
        } else {
            if (errno == EAGAIN || errno == EWOULDBLOCK)
                return RECV_WOULDBLOCK;
            return RECV_ERROR;
        }
    }

    return RECV_OK;
}
```

Our last step is to broadcast the message to all the clients and reset the message length variable of the client, as it was already read.

```c
// snippet of `handle_complete_message'
for (int i = 0; i <= server->clients.max_fd; i++) {
    Client* client = server->clients.connected[i];
    if (client == NULL)
        continue;

    const char* delim = ": ";
    uint32_t name_len = (uint32_t)strlen(current_client->name);
    uint32_t delim_len = 2;
    uint32_t msg_len = current_client->payload_length;

    uint32_t max_msg_len = MAX_MESSAGE_LENGTH;
    uint32_t available_for_msg = (name_len + delim_len < max_msg_len)
        ? (max_msg_len - name_len - delim_len)
        : 0;
    uint32_t msg_to_copy = msg_len > available_for_msg ? available_for_msg : msg_len;
    uint32_t out_len = name_len + delim_len + msg_to_copy;

    uint8_t* out = malloc(out_len);
    if (out == NULL) {
        perror("process_client: malloc failed");
        continue;
    }

    memcpy(out, current_client->name, name_len);
    memcpy(out + name_len, delim, delim_len);
    memcpy(out + name_len + delim_len, current_client->payload_buf, msg_to_copy);

    uint32_t netlen = htonl(out_len);
    send(client->sock, &netlen, 4, 0);
    send(client->sock, out, out_len, 0);

    free(out);

    printf(
        "process_client: client %d sent message to client %d of %u bytes (prefixed)\n",
        current_client->sock,
        client->sock,
        out_len
    );
}
```

As a conclusion of the way the server is handling *client sockets*, we have two important functions: recv that is the short form of the verb receive &** **send which is used for writing data to other clients.

## the client’s code

The important concepts of our server are explained; therefore, it is time to switch the focus to the **client**. The client is simply the program that lets a user connect to a chat server and use it.

Its main function is fairly simple; it accepts receiving the server’s address as *execution arguments* and then it makes use of the abstractions provided in the client.h and processing.h headers to connect & chat:

```c
static void usage(const char* prog) {
    fprintf(stderr, "usage: %s [-h host] [-p port]\n", prog);
}

int main(int argc, char* argv[]) {
    const char* host = DEFAULT_HOST;
    const char* port = DEFAULT_PORT;

    for (int i = 1; i < argc; i++) {
        if ((strcmp(argv[i], "-h") == 0 || strcmp(argv[i], "--host") == 0) && i + 1 < argc) {
            host = argv[++i];
        } else if ((strcmp(argv[i], "-p") == 0 || strcmp(argv[i], "--port") == 0) && i + 1 < argc) {
            port = argv[++i];
        } else if (strcmp(argv[i], "-?" ) == 0 || strcmp(argv[i], "--help") == 0) {
            usage(argv[0]);
            return 0;
        } else {
            usage(argv[0]);
            return 1;
        }
    }

    Client client = create_client(host, port);
    fprintf(stdout, "connected to %s:%s, first message sets your nickname\n", host, port);
    fflush(stdout);
    run_client(&client);
    destroy_client(&client);
    return 0;
}
```

### connecting 2 the server

This happens in a function called by create_client :

```c
static int connect_to_server(const char* host, const char* port) {
    struct addrinfo hints;
    struct addrinfo* res = NULL;
    struct addrinfo* it = NULL;
    int sock = -1;
    int rc;

    memset(&hints, 0, sizeof(hints));
    hints.ai_family = AF_UNSPEC;
    hints.ai_socktype = SOCK_STREAM;

    if ((rc = getaddrinfo(host, port, &hints, &res)) != 0) {
        fprintf(stderr, "getaddrinfo: %s\n", gai_strerror(rc));
        return -1;
    }

    for (it = res; it != NULL; it = it->ai_next) {
        sock = socket(
            it->ai_family, 
            it->ai_socktype, 
            it->ai_protocol
        );
        if (sock == -1) 
            continue;
        if (connect(sock, it->ai_addr, it->ai_addrlen) == 0) 
            break;
        close(sock);
        sock = -1;
    }

    freeaddrinfo(res);
    return sock;
}
```

The socket is still created using the function with the same name; however, binding & listening are no longer needed. Instead, we’re calling connect that returns 0 if the connection was successful.

### sending messages

Processing the client is way simpler, however, we’re still using **multiplexing**, which happens for two *fds*: **the socket &** **stdin**. Reading input from the terminal (through *stdin*) is blocking. We’re blocking the program until the user presses enter. Therefore, we’re waiting for that enter using the select function:

```c
// snippet of the 'run_client' function
// there's too much non-relevant code for including everything
char* line = NULL;
size_t line_cap = 0;
int stdin_fd = fileno(stdin); // getting the fd for stdin
while (1) {
    fd_set read_fds;
    FD_ZERO(&read_fds);
    FD_SET(stdin_fd, &read_fds);
    FD_SET(client->sock, &read_fds);
    int max_fd = client->sock > stdin_fd ? 
        client->sock : 
        stdin_fd;

    // multiplexingg
    int rc = select(max_fd + 1, &read_fds, NULL, NULL, NULL);
    if (rc < 0) {
        perror("select");
        break;
    }

    // looks like the user pressed 'enter'
    if (FD_ISSET(stdin_fd, &read_fds)) {
        ssize_t line_len = getline(&line, &line_cap, stdin);
```

After gathering the input, the protocol is simply followed by the client as well, the only difference being the fact that the htonl (host to network long) function is used instead for converting the length of the message to *network byte order*:

```c
int client_send_message(
    Client* client, 
    const uint8_t* payload, 
    uint32_t length
) {
    if (length == 0 || length > MAX_MESSAGE_LENGTH) {
        fprintf(stderr, "invalid message length: %u\n", length);
        return -1;
    }
    uint32_t netlen = htonl(length);
    if (send_all(client->sock, &netlen, 4) == -1) 
        return -1;
    if (send_all(client->sock, payload, length) == -1) 
        return -1;
    return 0;
}
```

The send_all function uses the send function while making sure that the entire message is sent:

```c
static int send_all(int sock, const void* buffer, size_t length) {
    const uint8_t* ptr = (const uint8_t*)buffer;
    size_t sent_total = 0;
    while (sent_total < length) {
        // 'send' function returns the number of bytes sent btw
        ssize_t n = send(sock, ptr + sent_total, length - sent_total, 0);
        if (n > 0) {
            sent_total += (size_t)n;
            continue;
        }
        if (n == -1 && errno == EINTR) 
            continue;
        return -1;
    }
    return 0;
}
```

To clear the console after writing a message, we simply use the following lines:

```c
// clear current line, move up, clear echoed input
fputs("\033[2K\r\033[1A\033[2K\r", stdout);
fflush(stdout);
```

### receiving

Receiving happens when the socket used for connecting to the server becomes readable:

```c
if (FD_ISSET(client->sock, &read_fds)) {
    if (recv_header_and_prepare_payload(client) == -1) {
        fprintf(stderr, "connection closed by server\n");
        break;
    }
    if (recv_payload(client) == -1) {
        fprintf(stderr, "connection closed by server\n");
        break;
    }

    if (
        client->payload_length > 0 && 
        client->payload_bytes_read == client->payload_length
    ) {
        handle_complete_message(
            client->payload_buf, 
            client->payload_length
        );
        reset_recv_state(client);
    }
}
```

As seen, this is similar to how this process was handled on the server.

## conclusion

This project strips networking down to its core: *sockets, bytes, and blocking I/O*. With select, one thread can handle many clients, and a simple length-prefixed protocol keeps data portable.

Once you understand this layer, everything else: *HTTP, WebSockets, gRPC*, is just an abstraction.
