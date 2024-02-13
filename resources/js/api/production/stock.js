import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/product/stock/data',
    method: 'get',
    params: query,
  });
}
export function createStock(data) {
  return request({
    url: '/production/product/stock/store',
    method: 'post',
    data,
  });
}
export function fetchListStokIn(id) {
  return request({
    url: '/production/product/stock/dataIN',
    method: 'post',
    data: { product_id: id },
  });
}
export function createStokOut(data) {
  return request({
    url: '/production/product/stock/storeOut',
    method: 'post',
    data,
  });
}
export function showMutasi(query) {
  return request({
    url: '/production/product/stock/showMutasi',
    method: 'get',
    params: query,
  });
}
export function showControlID(data) {
  return request({
    url: '/production/product/stock/showControlID',
    method: 'post',
    data,
  });
}
export function showByProduct(id) {
  return request({
    url: '/production/product/stock/showByProduct',
    method: 'post',
    data: {
      id: id,
    },
  });
}
