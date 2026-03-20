const { getDefaultConfig } = require('expo/metro-config');
const { withNativeWind } = require('nativewind/metro');
const path = require('path');

const config = getDefaultConfig(path.resolve(__dirname));

module.exports = withNativeWind(config, { input: './global.css' });