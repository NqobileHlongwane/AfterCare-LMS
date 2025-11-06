FROM php:8.2-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Expose the port Render expects
EXPOSE 10000

# Start PHP's built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
