import request from '@/utils/request';

export function fetchDeliveryOrder(query) {
  return request({
    url: '/vendor/delivery_order/data',
    method: 'get',
    params: query,
  });
}
export function fetchNoDO() {
  return request({
    url: '/vendor/delivery_order/fetchNoDO',
    method: 'get',
  });
}
export function createOrder(data) {
  return request({
    url: '/vendor/delivery_order/store',
    method: 'post',
    data,
  });
}
export function ShowDetailTransaction(id) {
  return request({
    url: '/vendor/delivery_order/showDetailTransaction',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function StartShipment(data) {
  return request({
    url: '/vendor/delivery_order/startShipment',
    method: 'post',
    data,
  });
}
export function ShowDelivery(id) {
  return request({
    url: '/vendor/delivery_order/showDelivery',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowDeliveryDetail(id) {
  return request({
    url: '/vendor/delivery_order/showDeliveryDetail',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowDeliveryByTransaction(id) {
  return request({
    url: '/vendor/delivery_order/showDeliveryByTransaction',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function fetchDeliveryBySupplier(data) {
  return request({
    url: '/vendor/delivery_order/dataDeliveryBySupplier',
    method: 'post',
    data,
  });
}
