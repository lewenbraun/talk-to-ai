#!/bin/sh

envsubst '${DOMAIN}' < /etc/nginx/conf.d/nginx.conf.template > /etc/nginx/conf.d/default.conf

# Parameters
DOMAIN="${DOMAIN}"
CERT_DIR="/etc/letsencrypt/live/${DOMAIN}"
TEMP_CERT_DIR="/etc/letsencrypt/live/temporary"
ACME_ROOT="/var/www/acme"

# Create necessary directories
mkdir -p "${TEMP_CERT_DIR}" "${ACME_ROOT}/.well-known/acme-challenge"
chown -R nginx:nginx "${ACME_ROOT}"
chmod -R 755 "${ACME_ROOT}"

# Create a temporary self-signed certificate if it does not exist
if [ ! -f "${TEMP_CERT_DIR}/privkey.pem" ]; then
  echo "Creating a temporary self-signed certificate"
  openssl req -x509 -nodes -newkey rsa:2048 \
    -days 1 \
    -keyout "${TEMP_CERT_DIR}/privkey.pem" \
    -out "${TEMP_CERT_DIR}/fullchain.pem" \
    -subj "/CN=localhost"
fi

# Check if a real certificate exists
if [ ! -f "${CERT_DIR}/fullchain.pem" ] || [ ! -f "${CERT_DIR}/privkey.pem" ]; then
  echo "No Let's Encrypt certificate found, starting Nginx with a temporary certificate for the ACME challenge"

  # Temporarily configure Nginx to use the temporary certificate
  sed -i "s|ssl_certificate .*|ssl_certificate ${TEMP_CERT_DIR}/fullchain.pem;|" /etc/nginx/conf.d/default.conf
  sed -i "s|ssl_certificate_key .*|ssl_certificate_key ${TEMP_CERT_DIR}/privkey.pem;|" /etc/nginx/conf.d/default.conf

  # Start Nginx in the background
  nginx -g "daemon on;"
  sleep 5  # Wait for Nginx to start

  # Request a real certificate
  echo "Requesting a real certificate from Let's Encrypt"
  certbot certonly --webroot \
    -w "${ACME_ROOT}" \
    -d "${DOMAIN}" \
    --email "${EMAIL}" \
    --non-interactive \
    --agree-tos

  # Check where the new certificate is stored
  if [ -f "${CERT_DIR}/fullchain.pem" ] && [ -f "${CERT_DIR}/privkey.pem" ]; then
    echo "New certificate obtained directly in ${CERT_DIR}"
    # Update Nginx configuration with the new certificate
    sed -i "s|ssl_certificate .*|ssl_certificate ${CERT_DIR}/fullchain.pem;|" /etc/nginx/conf.d/default.conf
    sed -i "s|ssl_certificate_key .*|ssl_certificate_key ${CERT_DIR}/privkey.pem;|" /etc/nginx/conf.d/default.conf
  else
    # Check an alternative location with ${DOMAIN}-*
    NEW_CERT=$(ls -d /etc/letsencrypt/live/${DOMAIN}-* 2>/dev/null | sort -V | tail -n 1)
    if [ -n "$NEW_CERT" ]; then
      echo "New certificate obtained: ${NEW_CERT}"
      # Update Nginx configuration with the new certificate
      sed -i "s|ssl_certificate .*|ssl_certificate ${NEW_CERT}/fullchain.pem;|" /etc/nginx/conf.d/default.conf
      sed -i "s|ssl_certificate_key .*|ssl_certificate_key ${NEW_CERT}/privkey.pem;|" /etc/nginx/conf.d/default.conf
    else
      echo "Failed to obtain a certificate, keeping the temporary one: ${TEMP_CERT_DIR}"
    fi
  fi

  # Stop the temporary Nginx
  nginx -s quit
  sleep 2  # Wait for shutdown
fi

# Configure automatic certificate renewal via cron
echo "Setting up automatic certificate renewal"
echo "0 0 * * * certbot renew --quiet && nginx -s reload" > /etc/crontabs/root

# Start cron in the background
crond -b &

# Start Nginx with the final certificate
echo "Starting Nginx with the final certificate"
exec nginx -g "daemon off;"
