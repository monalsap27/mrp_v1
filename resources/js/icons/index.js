import Vue from 'vue';
import SvgIcon from '@/components/SvgIcon';
import VueQRCodeComponent from 'vue-qrcode-component';
import { VueSignaturePad } from 'vue-signature-pad';

// register globally
// eslint-disable-next-line vue/component-definition-name-casing
Vue.component('svg-icon', SvgIcon);
Vue.component('QrCode', VueQRCodeComponent);
Vue.component('VueSignaturePad', VueSignaturePad);

const requireAll = (requireContext) =>
  requireContext.keys().map(requireContext);
const req = require.context('./svg', false, /\.svg$/);
requireAll(req);
