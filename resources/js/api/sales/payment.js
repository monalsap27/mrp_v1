import request from '@/utils/request';

export function createPayment(data) {
  return request({
    url: '/sales/payment/store',
    method: 'post',
    data,
  });
}
