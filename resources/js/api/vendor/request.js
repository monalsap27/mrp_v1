import request from '@/utils/request';

export function fetchListRequest(query) {
  return request({
    url: '/vendor/request/dataRequest',
    method: 'get',
    params: query,
  });
}
export function ShowRequest(id) {
  return request({
    url: '/vendor/request/showRequest',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowRequestDetail(id) {
  return request({
    url: '/vendor/request/showDetail',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function StoreConfirm(data) {
  return request({
    url: '/vendor/request/storeConfirm',
    method: 'post',
    data,
  });
}
export function RejectRequest(data) {
  return request({
    url: '/vendor/request/storeConfirm',
    method: 'post',
    data,
  });
}
export function showQtyVendor(id) {
  return request({
    url: '/vendor/request/showQtyVendor',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function fetchTransaction(query) {
  return request({
    url: '/vendor/request/dataTransaction',
    method: 'get',
    params: query,
  });
}
