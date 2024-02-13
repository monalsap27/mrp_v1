import request from '@/utils/request';

export function fetchNoDO() {
  return request({
    url: '/sales/delivery_order/fetchNoDO',
    method: 'get',
  });
}
export function createDeliveryOrder(data) {
  return request({
    url: '/sales/delivery_order/store',
    method: 'post',
    data,
  });
}
export function StartShipment(data) {
  return request({
    url: '/sales/delivery_order/startShipment',
    method: 'post',
    data,
  });
}
export function fetchDeliveryOrder(query) {
  return request({
    url: '/sales/delivery_order/data',
    method: 'get',
    params: query,
  });
}
export function fetchDeliveryByCustomer(data) {
  return request({
    url: '/sales/delivery_order/fetchDeliveryByCustomer',
    method: 'post',
    data,
  });
}
