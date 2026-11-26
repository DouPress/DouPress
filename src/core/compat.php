<?php
/**
 * PHP 5.x 兼容层（无其他依赖，安装页也可安全引入）
 */

// random_bytes 为 PHP 7.0+ 函数
if (!function_exists('random_bytes')) {
  function random_bytes($length)
  {
    if (function_exists('openssl_random_pseudo_bytes')) {
      $bytes = openssl_random_pseudo_bytes($length);
      if ($bytes !== false) {
        return $bytes;
      }
    }

    $bytes = '';
    for ($i = 0; $i < $length; $i++) {
      $bytes .= chr(mt_rand(0, 255));
    }

    return $bytes;
  }
}

// hash_equals 为 PHP 5.6+ 函数
if (!function_exists('hash_equals')) {
  function hash_equals($known_string, $user_string)
  {
    if (!is_string($known_string) || !is_string($user_string)) {
      return false;
    }
    if (strlen($known_string) !== strlen($user_string)) {
      return false;
    }

    $result = 0;
    for ($i = 0; $i < strlen($known_string); $i++) {
      $result |= ord($known_string[$i]) ^ ord($user_string[$i]);
    }

    return $result === 0;
  }
}

/**
 * 判断存储的密码是否已是哈希值（bcrypt，password_hash 兼容格式）
 */
function app_password_is_hashed($stored)
{
  return is_string($stored) && preg_match('/^\$2[aby]\$/', $stored);
}

/**
 * 校验明文密码与存储值（支持明文与 bcrypt 哈希两种存储格式）
 */
function app_verify_password($plain, $stored)
{
  if (app_password_is_hashed($stored)) {
    if (function_exists('password_verify')) {
      return password_verify($plain, $stored);
    }

    // PHP < 5.5 兼容：crypt 的 Blowfish 实现（$2y$，PHP 5.3.7+）
    return hash_equals($stored, crypt($plain, $stored));
  }

  return $stored === $plain;
}

/**
 * 密码哈希（存入配置前调用）
 */
function app_hash_password($plain)
{
  if (function_exists('password_hash')) {
    return password_hash($plain, PASSWORD_DEFAULT);
  }

  // PHP < 5.5 兼容：crypt 的 Blowfish 实现
  $salt = substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);

  return crypt($plain, '$2y$10$' . $salt);
}
