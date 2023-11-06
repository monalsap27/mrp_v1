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
