import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/sales/master/payment_method/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdatePaymentMethod(data) {
  return request({
    url: '/sales/master/payment_method/store',
    method: 'post',
    data,
  });
}

export function DeletePaymentMethod(id) {
  return request({
    url: '/sales/master/payment_method/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
