FROM ubuntu:22.04

RUN echo "fs.file-max = 2097152" >> /etc/sysctl.conf

COPY ./linux_unix_FineBI5_1-CN.sh /linux_unix_FineBI5_1-CN.sh

RUN chmod +x /linux_unix_FineBI5_1-CN.sh

RUN echo '\n\n\n1\n\n\n2048\n\n\n\n\n\n\nya\nn\n'  | /linux_unix_FineBI5_1-CN.sh

RUN rm /linux_unix_FineBI5_1-CN.sh

RUN sed -i 's/37799/80/g' /usr/local/FineBI5.1/server/conf/server.xml

COPY ./start.sh /start.sh

RUN chmod +x /start.sh

EXPOSE 80

CMD ["/bin/sh","/start.sh"]
