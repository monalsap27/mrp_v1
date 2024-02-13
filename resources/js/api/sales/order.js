import request from '@/utils/request';

export function fetchList() {
  return request({
    url: '/sales/order/data',
    method: 'get',
  });
}
export function fetchNoSO() {
  return request({
    url: '/sales/order/fetchNoSO',
    method: 'get',
  });
}
export function createOrder(data) {
  return request({
    url: '/sales/order/store',
    method: 'post',
    data,
  });
}
export function ShowOrder(id) {
  return request({
    url: '/sales/order/show',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowOrderDetail(id) {
  return request({
    url: '/sales/order/showDetail',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ConfirmManufacture(data) {
  return request({
    url: '/sales/order/confirmManufacture',
    method: 'post',
    data,
  });
}
export function ConfirmPacked(data) {
  return request({
    url: '/sales/order/confirmPacked',
    method: 'post',
    data,
  });
}
export function ShowDetailByOrder(id) {
  return request({
    url: '/sales/order/showDetailByOrder',
    method: 'POST',
    data: {
      id: id,
    },
  });
}
export function fetchListRequest() {
  return request({
    url: '/sales/order/dataRequest',
    method: 'get',
  });
}
