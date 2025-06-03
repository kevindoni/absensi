module.exports = {
  apps: [
    {
      name: 'whatsapp-service',
      script: 'server.js',
      cwd: process.env.WHATSAPP_SERVICE_PATH || process.cwd(),
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'production',
        PORT: 3001
      },
      env_development: {
        NODE_ENV: 'development',
        PORT: 3001
      },
      error_file: './logs/err.log',
      out_file: './logs/out.log',
      log_file: './logs/combined.log',
      time: true,
      restart_delay: 5000,
      max_restarts: 10,
      min_uptime: '10s'
    }
  ]
};
