FROM tta-backend-image as builder

FROM nginx:stable-alpine

COPY --from=builder /var/www/public /var/www/public

RUN apk add --no-cache certbot openssl bash busybox-openrc gettext

COPY .docker/nginx/prod/nginx.conf.template /etc/nginx/conf.d/nginx.conf.template
COPY .docker/nginx/prod/entrypoint.sh /opt/entrypoint.sh

RUN chmod +x /opt/entrypoint.sh && \
    mkdir -p /var/www/acme && \
    chown -R nginx:nginx /var/www/acme && \
    chmod -R 755 /var/www/acme

CMD ["/opt/entrypoint.sh"]