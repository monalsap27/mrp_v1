import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/master/unit/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateUnit(data) {
  return request({
    url: '/production/master/unit/store',
    method: 'post',
    data,
  });
}

export function DeleteUnit(id) {
  return request({
    url: '/production/master/unit/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
