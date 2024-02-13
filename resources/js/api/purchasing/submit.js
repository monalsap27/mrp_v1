import request from '@/utils/request';

export function storeSubmitPO(data) {
  return request({
    url: '/purchasing/submit/store',
    method: 'post',
    data,
  });
}
export function fetchList(query) {
  return request({
    url: '/purchasing/submit/data',
    method: 'get',
    params: query,
  });
}
export function storeReject(data) {
  return request({
    url: '/purchasing/submit/storeReject',
    method: 'post',
    data,
  });
}
export function fetchSubmitBySupplier(id) {
  return request({
    url: '/purchasing/submit/dataSubmitBySupplier',
    method: 'post',
    params: {
      id: id,
    },
  });
}
