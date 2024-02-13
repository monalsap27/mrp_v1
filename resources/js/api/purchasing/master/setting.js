import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/purchasing/master/setting/data',
    method: 'get',
    params: query,
  });
}
export function createOrUpdateSetting(data) {
  return request({
    url: '/purchasing/master/setting/store',
    method: 'post',
    data,
  });
}
export function DeleteSetting(id) {
  return request({
    url: '/purchasing/master/setting/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function fetchProductSettingBySupplier(id) {
  return request({
    url: '/purchasing/master/setting/fetchProductSettingBySupplier',
    method: 'post',
    params: {
      id: id,
    },
  });
}
