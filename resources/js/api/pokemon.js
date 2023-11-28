import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: 'https://pokeapi.co/api/v2/ability',
    method: 'get',
    params: query,
  });
}
