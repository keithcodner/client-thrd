const { getDefaultConfig } = require('expo/metro-config');
const { withNativeWind } = require('nativewind/metro');
const path = require('path');

// Suppress noisy React Native Web SSR warnings in Metro server
const originalError = console.error;
const originalWarn = console.warn;

console.error = (...args) => {
  const msg = args[0]?.toString() || '';
  if (
    msg.includes('non-boolean attribute') && msg.includes('collapsable')
  ) return;
  originalError(...args);
};

console.warn = (...args) => {
  const msg = args[0]?.toString() || '';
  if (
    msg.includes('props.pointerEvents is deprecated') ||
    msg.includes('shadow*" style props are deprecated')
  ) return;
  originalWarn(...args);
};

const config = getDefaultConfig(path.resolve(__dirname));

module.exports = withNativeWind(config, { input: './global.css' });