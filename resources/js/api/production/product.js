import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/product/data',
    method: 'get',
    params: query,
  });
}
export function fetchListApproval(query) {
  return request({
    url: '/production/product/dataApproval',
    method: 'get',
    params: query,
  });
}
export function comboData(query) {
  return request({
    url: '/production/product/comboData',
    method: 'get',
    params: query,
  });
}
export function createProduct(data) {
  return request({
    url: '/production/product/store',
    method: 'post',
    data,
  });
}
export function DeleteCategories(id) {
  return request({
    url: '/production/product/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowProduct(id) {
  return request({
    url: '/production/product/show',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ShowProductDetail(id) {
  return request({
    url: '/production/product/showDetail',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function DeleteProduct(id) {
  return request({
    url: '/production/product/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function ApproveProduct(id) {
  return request({
    url: '/production/product/approve',
    method: 'post',
    data: {
      id: id,
      status: 2,
    },
  });
}
export function RejectProduct(data) {
  return request({
    url: '/production/product/approve',
    method: 'post',
    data,
  });
}
export function dataShowBillOf(query) {
  return request({
    url: '/production/product/dataShowBillOf',
    method: 'get',
    params: query,
  });
}
export function updateSafetyStock(data) {
  return request({
    url: '/production/product/updateSafetyStock',
    method: 'post',
    data,
  });
}
