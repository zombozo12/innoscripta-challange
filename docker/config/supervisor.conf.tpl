[supervisord]
nodaemon=true
user=root
logfile=/dev/stdout
logfile_maxbytes=0
pidfile=/run/supervisord.pid

[program:queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php %%PWD%%/artisan queue:work
autostart=true
autorestart=true
numprocs=1
startretries=10
stdout_events_enabled=1
redirect_stderr=true
stopasgroup=true
killasgroup=true
user=%%USER%%
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile_maxbytes=0
stopwaitsecs=3600

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[program:php-fpm]
command=php-fpm -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[inet_http_server]
port = 0.0.0.0:9001
username = root
password = root
