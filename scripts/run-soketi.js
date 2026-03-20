const { spawn } = require('child_process');
const path = require('path');

// Run the batch file that switches to Node 18 and starts soketi
const batchFile = path.join(__dirname, 'start-soketi.bat');
const child = spawn('cmd.exe', ['/c', batchFile], {
  stdio: 'inherit',
  shell: true
});

child.on('exit', (code) => {
  process.exit(code || 0);
});
