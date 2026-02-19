# Gunakan image resmi PHP dengan Apache
FROM php:8.2-apache

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Copy semua file project kamu ke dalam container
COPY . /var/www/html/

# Bikin folder uploads dan file config.json kosong (buat jaga-jaga kalau belum ada)
RUN mkdir -p /var/www/html/uploads && \
    touch /var/www/html/config.json

# PENTING: Ubah kepemilikan folder agar PHP (www-data) punya izin untuk nulis file
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/uploads && \
    chmod 664 /var/www/html/config.json

# Bikin custom konfigurasi PHP untuk memperbesar limit upload gambar (biar gak error kayak di Mac kemarin)
RUN echo "file_uploads = On\n\
memory_limit = 128M\n\
upload_max_filesize = 50M\n\
post_max_size = 50M\n\
max_execution_time = 60\n" > /usr/local/etc/php/conf.d/custom-uploads.ini

# Expose port 80 bawaan Apache
EXPOSE 80