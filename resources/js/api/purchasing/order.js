import request from '@/utils/request';

export function fetchList() {
  return request({
    url: '/purchasing/order/data',
    method: 'get',
  });
}
export function fetchListApproval() {
  return request({
    url: '/purchasing/order/dataApproval',
    method: 'get',
  });
}
export function fetchNoPO() {
  return request({
    url: '/purchasing/order/fetchNoPO',
    method: 'get',
  });
}
export function createOrder(data) {
  return request({
    url: '/purchasing/order/store',
    method: 'post',
    data,
  });
}
export function ShowOrder(id) {
  return request({
    url: '/purchasing/order/show',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowOrderDetail(id) {
  return request({
    url: '/purchasing/order/showDetail',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function DeleteOrder(id) {
  return request({
    url: '/purchasing/order/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ApproveOrders(id) {
  return request({
    url: '/purchasing/order/approve',
    method: 'post',
    data: {
      id: id,
      status: 2,
    },
  });
}
export function RejectOrders(data) {
  return request({
    url: '/purchasing/order/approve',
    method: 'post',
    data,
  });
}
export function showDataDeliveryInbound(id) {
  return request({
    url: '/purchasing/order/showDataDeliveryInbound',
    method: 'post',
    data: {
      id: id,
      status: 2,
    },
  });
}
export function confirmInboundPO(data) {
  return request({
    url: '/purchasing/order/confirmInboundPO',
    method: 'post',
    data,
  });
}
