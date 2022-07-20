<?php


namespace app\common;


class ApiCode
{
    /** @var int 成功 */
    const CODE_SUCCESS = 0;

    /** @var int 失败 */
    const CODE_ERROR = 1;

    /** @var int 未登录 */
    const CODE_NOT_LOGIN = -1;

    /** @var int app禁用 */
    const CODE_STORE_DISABLED = -2;

    /** @var int 未关注公众号 */
    const CODE_WECHAT_SUBSCRIBE = 2;
}