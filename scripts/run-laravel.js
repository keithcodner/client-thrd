const { spawn } = require('child_process');
const path = require('path');

// Run the batch file that starts Laravel
const batchFile = path.join(__dirname, 'start-laravel.bat');
const child = spawn('cmd.exe', ['/c', batchFile], {
  stdio: 'inherit',
  shell: true
});

child.on('exit', (code) => {
  process.exit(code || 0);
});
