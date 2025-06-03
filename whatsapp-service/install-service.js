const Service = require('node-windows').Service;
const path = require('path');

// Create a new service object
const svc = new Service({
  name: 'WhatsApp Baileys Service',
  description: 'WhatsApp Gateway service using Baileys for Laravel Attendance System',
  script: path.join(__dirname, 'server.js'),
  nodeOptions: [
    '--harmony',
    '--max_old_space_size=4096'
  ],
  workingDirectory: __dirname,
  allowServiceLogon: true
});

// Listen for the "install" event, which indicates the
// process is available as a service.
svc.on('install', function(){
  console.log('WhatsApp Service installed successfully!');
  console.log('Starting service...');
  svc.start();
});

svc.on('alreadyinstalled', function(){
  console.log('WhatsApp Service is already installed.');
});

svc.on('start', function(){
  console.log('WhatsApp Service started successfully!');
  console.log('Service Name: ' + svc.name);
  console.log('Service Status: Running');
});

svc.on('error', function(err){
  console.error('Service Error:', err);
});

// Install the service
console.log('Installing WhatsApp Service...');
svc.install();
