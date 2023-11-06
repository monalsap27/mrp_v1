import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/master/supplier/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateSupplier(data) {
  return request({
    url: '/production/master/supplier/store',
    method: 'post',
    data,
  });
}

export function DeleteSupplier(id) {
  return request({
    url: '/production/master/supplier/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
