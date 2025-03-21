FROM tta-backend-image as builder

FROM nginx:stable-alpine

COPY --from=builder /var/www/public /var/www/public

RUN rm /etc/nginx/conf.d/default.conf

COPY .docker/nginx/nginx.conf /etc/nginx/conf.d