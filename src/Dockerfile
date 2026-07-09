FROM debian:13

RUN apt update && apt install -y g++ gdb gcc coreutils perl grep sed && rm -rf /var/lib/apt/lists*

RUN useradd -m runner

USER runner

WORKDIR /home/runner

CMD ["bash"]
