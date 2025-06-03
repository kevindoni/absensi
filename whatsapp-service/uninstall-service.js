const Service = require('node-windows').Service;
const path = require('path');

// Create a new service object
const svc = new Service({
  name: 'WhatsApp Baileys Service',
  description: 'WhatsApp Gateway service using Baileys for Laravel Attendance System',
  script: path.join(__dirname, 'server.js')
});

// Listen for the "uninstall" event
svc.on('uninstall', function(){
  console.log('WhatsApp Service uninstalled successfully!');
});

svc.on('stop', function(){
  console.log('WhatsApp Service stopped.');
});

svc.on('error', function(err){
  console.error('Service Error:', err);
});

// Uninstall the service
console.log('Uninstalling WhatsApp Service...');
svc.uninstall();
