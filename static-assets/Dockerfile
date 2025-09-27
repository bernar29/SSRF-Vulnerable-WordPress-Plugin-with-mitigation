FROM python:3.12-slim
WORKDIR /srv
# copy a local "public" dir
COPY public ./public
COPY entrypoint.sh .
RUN chmod +x entrypoint.sh
EXPOSE 8000
CMD ["./entrypoint.sh"]
