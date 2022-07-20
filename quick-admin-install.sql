/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : quick_serve

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 20/07/2022 15:20:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for qk_system_admin_info
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_admin_info`;
CREATE TABLE `qk_system_admin_info`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '员工id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号id',
  `plugin_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '模块标识',
  `auth_set` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权限',
  `email` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '员工邮箱',
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '员工手机号',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '员工姓名',
  `nickname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '员工昵称',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '员工头像',
  `gender` enum('male','female','unknow') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unknow' COMMENT '员工性别',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1启用,0:禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除:0=未删除,1=删除',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统管理员-员工' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_admin_info
-- ----------------------------
INSERT INTO `qk_system_admin_info` VALUES (6, 45, 'admin', '', '', '', 'admin', 'admin', '', 'unknow', 1, 0, '2022-07-20 14:59:47', '2022-07-20 14:59:47');

-- ----------------------------
-- Table structure for qk_system_area
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_area`;
CREATE TABLE `qk_system_area`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) NULL DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简称',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `mergename` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) NULL DEFAULT NULL COMMENT '层级:0=省,1=市,2=区县',
  `pinyin` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '长途区号',
  `zip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纬度',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3750 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '地区表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_attachment
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_attachment`;
CREATE TABLE `qk_system_attachment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `storage_id` int(11) NOT NULL COMMENT '仓储ID',
  `attachment_cate_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名称',
  `size` int(11) NOT NULL COMMENT '大小字节',
  `image` varchar(2080) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `thumb_image` varchar(2080) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL COMMENT '类型:1=图片,2=视频',
  `is_recycle` tinyint(1) NOT NULL DEFAULT 0 COMMENT '加入回收站:0=否,1=是',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除:0=未删除,1=已删除',
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cate`(`attachment_cate_id`) USING BTREE,
  INDEX `idx_type`(`type`) USING BTREE,
  INDEX `idx_deleted`(`is_deleted`) USING BTREE,
  INDEX `idx_recycle`(`is_recycle`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_attachment_cate
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_attachment_cate`;
CREATE TABLE `qk_system_attachment_cate`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storage_id` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父级ID',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名称',
  `is_recycle` tinyint(1) NOT NULL DEFAULT 0 COMMENT '加入回收站:0=否,1=是',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除:0=未删除,1=已删除',
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_attachment_storage
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_attachment_storage`;
CREATE TABLE `qk_system_attachment_storage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件标识',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除:0=未删除,1=已删除',
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件仓库表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_auth
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_auth`;
CREATE TABLE `qk_system_auth`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '系统插件plugin_name',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `node_set` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '权限节点集合 多个值,号隔开',
  `desc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '说明',
  `level` smallint(5) NOT NULL DEFAULT 1 COMMENT '管理级别',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态(0:禁用,1:启用)',
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_plugin_id`(`plugin_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_auth_node
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_auth_node`;
CREATE TABLE `qk_system_auth_node`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `auth` bigint(20) UNSIGNED NULL DEFAULT 0 COMMENT '角色',
  `node_id` int(10) NULL DEFAULT 0,
  `node` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '节点',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_auth_auth`(`auth`) USING BTREE,
  INDEX `idx_system_auth_node`(`node`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5795 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-授权' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_config
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_config`;
CREATE TABLE `qk_system_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '分组',
  `plugin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin' COMMENT '插件标识',
  `config_type` enum('code','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin' COMMENT '配置类型:code=代码配置,admin=后台配置',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '类型:string,text,int,bool,json,datetime,date,file',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '配置标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置简介',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '配置名',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '变量字典数据',
  `rule` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '验证规则',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1=显示,0=隐藏',
  `sort` int(10) NOT NULL DEFAULT 1 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_config
-- ----------------------------
INSERT INTO `qk_system_config` VALUES (4, 'base', 'admin', 'admin', 'upload_image', '系统logo', '', 'app_logo', 'http://demo.quickadmin.cn/upload/e9/d8b00136b434694ae5c7d9070dcab5.png', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 0);
INSERT INTO `qk_system_config` VALUES (5, 'storage', 'admin', 'admin', 'text', 'alioss_point', NULL, 'alioss_point', 'oss-cn-s', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 0);
INSERT INTO `qk_system_config` VALUES (6, 'storage', 'admin', 'admin', 'text', 'alioss_bucket', NULL, 'alioss_bucket', 'videomp4zho', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 0);
INSERT INTO `qk_system_config` VALUES (7, 'storage', 'admin', 'admin', 'text', 'alioss_access_key', NULL, 'alioss_access_key', 'LTAI4Fg6v93TJQpZv', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 0);
INSERT INTO `qk_system_config` VALUES (8, 'storage', 'admin', 'admin', 'text', 'alioss_secret_key', NULL, 'alioss_secret_key', 'vqxCHJpyviawzU3ztvO1', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 0);
INSERT INTO `qk_system_config` VALUES (9, 'storage', 'admin', 'admin', 'radio', 'alioss_http_protocol', NULL, 'alioss_http_protocol', 'https', '{\"https\":\"https\",\"http\":\"http\"}', '', 1, 99);
INSERT INTO `qk_system_config` VALUES (10, 'storage', 'admin', 'admin', 'text', 'alioss_http_domain', NULL, 'alioss_http_domain', '', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 3);
INSERT INTO `qk_system_config` VALUES (11, 'storage', 'admin', 'admin', 'radio', '储存类型', '', 'storage_type', 'local', 'local=本地\nalioss=阿里', '', 1, 100);
INSERT INTO `qk_system_config` VALUES (12, 'base', 'admin', 'admin', 'text', '系统名称', NULL, 'app_name', 'QuickAdmin', '{\"value1\":\"title1\",\"value2\":\"title2\"}', '', 1, 1);
INSERT INTO `qk_system_config` VALUES (19, 'wxapp', 'admin', 'admin', 'text', '小程序AppId', '', 'appid', 'wxa', '', '', 1, 1);
INSERT INTO `qk_system_config` VALUES (20, 'wxapp', 'admin', 'admin', 'text', '小程序AppSecret', '', 'app_secret', '8287f79885', '', '', 1, 1);
INSERT INTO `qk_system_config` VALUES (21, 'base', 'mall', 'code', 'string', '基础设置', '配置', 'sted', '2', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (22, 'base', 'mall', 'code', 'string', '基础设置', '配置', 'tesst', '3', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (24, 'show', 'mall', 'code', 'string', '显示配置', '显示配置', 'sted', '56', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (25, 'show', 'mall', 'code', 'string', '显示配置', '显示配置', 'tesst', '77', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (26, 'base3', 'mall', 'code', 'string', '支付方式', '支付方式', 'test', '343434', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (27, 'order', 'mall', 'code', 'string', '订单配置', '订单配置', 'sted', '4', NULL, '', 1, 1);
INSERT INTO `qk_system_config` VALUES (28, 'order', 'mall', 'code', 'string', '订单配置', '订单配置', 'tesst', '6', NULL, '', 1, 1);

-- ----------------------------
-- Table structure for qk_system_config_group
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_config_group`;
CREATE TABLE `qk_system_config_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父级ID',
  `group` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组变量名称',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组别名',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态:1=启用,0=禁用',
  `show` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '显示:1=显示,0=隐藏',
  `type` int(2) NULL DEFAULT 0 COMMENT '分组类型',
  `sort` int(11) NOT NULL DEFAULT 100 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统-配置-分组' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_config_group
-- ----------------------------
INSERT INTO `qk_system_config_group` VALUES (3, 0, 'wxapp', '小程序配置', 1, 0, 0, 100);
INSERT INTO `qk_system_config_group` VALUES (28, 0, 'quick', '系统配置', 1, 0, 0, 200);
INSERT INTO `qk_system_config_group` VALUES (29, 28, 'storage', '上传配置', 1, 0, 0, 99);
INSERT INTO `qk_system_config_group` VALUES (30, 28, 'base', '基本配置', 1, 0, 0, 100);

-- ----------------------------
-- Table structure for qk_system_group
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_group`;
CREATE TABLE `qk_system_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '组合数据ID',
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '模块插件',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '数据组名称',
  `info` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '数据提示',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '数据字段名称',
  `fields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据组字段（json数据）',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '组合数据表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_group_data
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_group_data`;
CREATE TABLE `qk_system_group_data`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '模块插件',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '数据组',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据组数据（json数据）',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '数据排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态1：开启；0：关闭',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_plugin_name`(`plugin`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '组合数据详情' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_jobs
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_jobs`;
CREATE TABLE `qk_system_jobs`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `queue` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '队列名称',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '有效负载',
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重试次数',
  `reserved` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订阅次数',
  `reserve_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '订阅时间',
  `available_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效时间',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1585 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-消息列的' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_menu`;
CREATE TABLE `qk_system_menu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '系统插件plugin_name ',
  `pid` bigint(20) UNSIGNED NULL DEFAULT 0 COMMENT '上级ID',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单图标',
  `badge` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'badge',
  `node` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '节点代码',
  `path` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '链接节点',
  `params` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '_self' COMMENT '打开方式 _blank _self',
  `sort` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '排序权重',
  `is_admin` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '平台菜单 1',
  `level` smallint(5) UNSIGNED NULL DEFAULT 1 COMMENT '管理级别',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态(0:禁用,1:启用)',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_node`(`node`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE,
  INDEX `idx_plugin_name`(`plugin_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 197 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_menu
-- ----------------------------
INSERT INTO `qk_system_menu` VALUES (3, 'admin', 64, '菜单管理', 'redenvelope-fill', '', '', 'admin/resource/menus/index', '', '_self', 0, 0, 1, 1, '2021-06-22 22:52:17', '2021-06-22 22:52:17');
INSERT INTO `qk_system_menu` VALUES (10, 'admin', 75, '权限管理', 'el-icon-Coin', '', '', '#', '', '_self', 20, 0, 1, 1, '2022-01-13 11:42:34', '2022-01-13 11:42:34');
INSERT INTO `qk_system_menu` VALUES (14, 'admin', 10, '权限管理', 'radius-bottomright', '', '', 'admin/resource/auth/index', '', '_self', 0, 0, 1, 1, '2021-10-02 10:45:14', '2021-10-02 10:45:14');
INSERT INTO `qk_system_menu` VALUES (16, 'admin', 10, '节点管理', 'interation-fill', '', '', 'admin/resource/node/index', '', '_self', 0, 0, 1, 0, '2022-01-21 14:57:35', '2022-03-15 16:51:22');
INSERT INTO `qk_system_menu` VALUES (23, 'admin', 75, '首页', 'el-icon-house', '', '', 'dashboard', '', '_self', 25, 0, 1, 1, '2022-01-21 14:59:52', '2022-01-21 14:59:52');
INSERT INTO `qk_system_menu` VALUES (27, 'admin', 64, '系统日志', 'deleteteam', '', '', 'admin/resource/oplog/index', '', '_self', 7, 0, 1, 1, '2021-06-22 22:51:58', '2021-06-22 22:51:58');
INSERT INTO `qk_system_menu` VALUES (28, 'admin', 10, '管理员工', 'project-fill', '', '', 'admin/resource/admin/index', '', '_self', 1, 0, 1, 1, '2021-10-01 12:20:30', '2021-10-01 12:20:30');
INSERT INTO `qk_system_menu` VALUES (64, 'admin', 75, '系统配置', 'el-icon-Setting', '', '', '#', '', '_self', 22, 0, 1, 1, '2022-01-13 11:42:04', '2022-01-13 11:42:04');
INSERT INTO `qk_system_menu` VALUES (65, 'admin', 64, '系统配置', 'border-inner', '', '', 'admin/resource/system_config/index', '', '_self', 1, 0, 1, 1, '2021-10-07 20:55:34', '2021-10-07 20:55:34');
INSERT INTO `qk_system_menu` VALUES (67, 'admin', 84, '系统任务', '', '', '', 'admin/resource/system_queue/index', '', '_self', 1, 0, 1, 1, '2021-10-03 16:06:37', '2022-04-17 21:58:49');
INSERT INTO `qk_system_menu` VALUES (69, 'admin', 84, '组合配置', '', '', '', 'admin/resource/group_new/index', '', '_self', 1, 0, 1, 1, '2022-01-20 14:31:58', '2022-01-20 14:31:58');
INSERT INTO `qk_system_menu` VALUES (70, 'admin', 64, '组合数据', 'border-verticle', '', '', 'admin/resource/group/index', '', '_self', 1, 0, 1, 0, '2021-12-27 16:29:20', '2021-12-27 16:29:20');
INSERT INTO `qk_system_menu` VALUES (75, 'admin', 0, '系统管理', 'el-icon-Setting', '', '', 'admin', '', '_self', 400, 0, 1, 1, '2022-01-23 21:59:51', '2022-01-23 21:59:51');
INSERT INTO `qk_system_menu` VALUES (84, 'admin', 75, '系统维护', 'el-icon-Setting', '', '', '#', '', '_self', 1, 0, 1, 1, '2022-01-04 20:28:26', '2022-01-04 20:28:26');
INSERT INTO `qk_system_menu` VALUES (85, 'admin', 84, '代码crud', '', '', '', 'crud/resource/index/index', '', '_self', 4, 0, 1, 1, '2022-01-17 16:58:27', '2022-01-17 16:58:27');
INSERT INTO `qk_system_menu` VALUES (87, 'admin', 84, '系统配置', '', '', '', 'admin/resource/config/index', '', '_self', 1, 0, 1, 1, '2022-01-19 09:12:28', '2022-01-19 09:12:28');
INSERT INTO `qk_system_menu` VALUES (88, 'admin', 75, '用户管理', 'el-icon-User', '', '', 'admin/resource/system_user/index', '', '_self', 1, 0, 1, 0, '2022-01-28 17:29:00', '2022-04-12 16:06:44');
INSERT INTO `qk_system_menu` VALUES (167, 'admin', 84, '系统更新', '', '', '', 'admin/resource/upgrade/index', '', '_self', 1, 0, 1, 1, '2022-04-17 21:58:26', '2022-04-17 21:58:26');

-- ----------------------------
-- Table structure for qk_system_node
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_node`;
CREATE TABLE `qk_system_node`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '系统插件plugin_name',
  `node` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '节点规则',
  `pnode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '父节点',
  `mode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'controller  resource',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `condition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `level` tinyint(1) NOT NULL DEFAULT 1 COMMENT '节点层级',
  `is_menu` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否可设置为菜单',
  `is_auth` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否启动RBAC权限控制',
  `is_login` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否启动登录控制',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态(0:禁用,1:启用)',
  `sort` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `node`(`node`) USING BTREE,
  INDEX `idx_node`(`node`) USING BTREE,
  INDEX `idx_plugin_id`(`plugin_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 231 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '节点表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_oplog
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_oplog`;
CREATE TABLE `qk_system_oplog`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `node` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `geoip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `action` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作行为名称',
  `content` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作内容描述',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 147 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_oplog
-- ----------------------------
INSERT INTO `qk_system_oplog` VALUES (146, 'admin/passport/login', '127.0.0.1', 'login', '登录系统', 'admin', '2022-07-20 15:00:01');

-- ----------------------------
-- Table structure for qk_system_plugin
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_plugin`;
CREATE TABLE `qk_system_plugin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '插件key',
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '显示名称',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '描述',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '版本号',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1:启用, 0:禁用',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除: 1已删除 0未删除',
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `create_by` int(11) NOT NULL DEFAULT 0 COMMENT '创建人admin_id',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  `update_by` int(11) NOT NULL DEFAULT 0 COMMENT '修改人admin_id',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统插件' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_queue
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_queue`;
CREATE TABLE `qk_system_queue`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务编号',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务名称',
  `command` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '执行指令',
  `exec_pid` bigint(20) NULL DEFAULT 0 COMMENT '执行进程',
  `exec_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '执行参数',
  `exec_time` bigint(20) NULL DEFAULT 0 COMMENT '执行时间',
  `exec_desc` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '执行描述',
  `enter_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '开始时间',
  `outer_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '结束时间',
  `loops_time` bigint(20) NULL DEFAULT 0 COMMENT '循环时间',
  `attempts` bigint(20) NULL DEFAULT 0 COMMENT '执行次数',
  `rscript` tinyint(1) NULL DEFAULT 1 COMMENT '任务类型(0单例,1多例)',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
  `create_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_queue_code`(`code`) USING BTREE,
  INDEX `idx_system_queue_title`(`title`) USING BTREE,
  INDEX `idx_system_queue_status`(`status`) USING BTREE,
  INDEX `idx_system_queue_rscript`(`rscript`) USING BTREE,
  INDEX `idx_system_queue_create_at`(`create_at`) USING BTREE,
  INDEX `idx_system_queue_exec_time`(`exec_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-任务' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_queue2
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_queue2`;
CREATE TABLE `qk_system_queue2`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务编号',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务名称',
  `queue_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '列队',
  `exec_pid` bigint(20) NULL DEFAULT 0 COMMENT '执行进程',
  `exec_data` blob NOT NULL COMMENT '执行参数',
  `exec_time` bigint(20) NULL DEFAULT 0 COMMENT '执行时间',
  `exec_desc` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '执行描述',
  `enter_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '开始时间',
  `outer_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '结束时间',
  `loops_time` bigint(20) NULL DEFAULT 0 COMMENT '循环时间',
  `attempts` bigint(20) NULL DEFAULT 0 COMMENT '执行次数',
  `rscript` tinyint(1) NULL DEFAULT 1 COMMENT '任务类型(0单例,1多例)',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
  `create_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_queue_code`(`code`) USING BTREE,
  INDEX `idx_system_queue_title`(`title`) USING BTREE,
  INDEX `idx_system_queue_status`(`status`) USING BTREE,
  INDEX `idx_system_queue_rscript`(`rscript`) USING BTREE,
  INDEX `idx_system_queue_create_at`(`create_at`) USING BTREE,
  INDEX `idx_system_queue_exec_time`(`exec_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-任务' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_queue3
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_queue3`;
CREATE TABLE `qk_system_queue3`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务编号',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务名称',
  `queue` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '队列名称',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '有效负载',
  `command` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '执行指令',
  `run_pid` bigint(20) NULL DEFAULT 0 COMMENT '执行进程',
  `available_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效时间',
  `reserve_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '执行时间',
  `enter_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '开始时间',
  `outer_time` decimal(20, 4) NULL DEFAULT 0.0000 COMMENT '结束时间',
  `desc` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '执行描述',
  `attempts_max` int(10) NOT NULL DEFAULT 3 COMMENT '重发最大次数',
  `loops_time` bigint(20) NULL DEFAULT 0 COMMENT '循环时间',
  `attempts` bigint(20) NULL DEFAULT 0 COMMENT '执行次数',
  `rscript` tinyint(1) NULL DEFAULT 1 COMMENT '任务类型(0单例,1多例)',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_queue_code`(`code`) USING BTREE,
  INDEX `idx_system_queue_title`(`title`) USING BTREE,
  INDEX `idx_system_queue_status`(`status`) USING BTREE,
  INDEX `idx_system_queue_rscript`(`rscript`) USING BTREE,
  INDEX `idx_system_queue_create_at`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 64 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-任务' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_user
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_user`;
CREATE TABLE `qk_system_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账号id',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '账户名称',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1:启用, 0:禁用',
  `last_login_ip_at` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '最后一次登录ip',
  `create_ip_at` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '创建ip',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `login_fail_num` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除: 1已删除 0未删除',
  `login_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` datetime(0) NOT NULL DEFAULT '1990-01-01 00:00:00' COMMENT '删除日期',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_email`(`email`) USING BTREE,
  INDEX `idx_phone`(`phone`) USING BTREE,
  INDEX `idx_username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 46 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统统一账户' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_user
-- ----------------------------
INSERT INTO `qk_system_user` VALUES (45, 'admin', 'admin', '', '', '', '333ead5b69792d0b7feb64d965edb788', 'r4c6', 1, '', '', 0, 0, 0, '2022-07-20 15:00:01', '1990-01-01 00:00:00', '2022-07-20 14:59:47', '2022-07-20 15:00:01');

-- ----------------------------
-- Table structure for qk_system_user_identity
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_user_identity`;
CREATE TABLE `qk_system_user_identity`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户身份表',
  `user_id` int(11) NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为超级管理员',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为管理员',
  `is_operator` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为操作员|员工',
  `member_level` int(11) NOT NULL DEFAULT 0 COMMENT '会员等级:0.普通成员',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 已删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `is_super_admin`(`is_super_admin`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '账号身份表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qk_system_user_identity
-- ----------------------------
INSERT INTO `qk_system_user_identity` VALUES (28, 45, 1, 1, 0, 0, 0);

-- ----------------------------
-- Table structure for qk_system_user_info
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_user_info`;
CREATE TABLE `qk_system_user_info`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号id',
  `gender` enum('male','female','unknow') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unknow' COMMENT '性别',
  `integral` int(11) NOT NULL DEFAULT 0 COMMENT '积分',
  `total_integral` int(11) NOT NULL DEFAULT 0 COMMENT '最高积分',
  `balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `total_balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总余额',
  `contact_way` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系方式',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级id',
  `temp_parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '临时上级',
  `platform_openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '平台id 如微信 openid',
  `platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户所属平台标识 facebook,google,wechat,qq,weibo,twitter,weapp',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id`) USING BTREE,
  INDEX `idx_temp_parent_id`(`temp_parent_id`) USING BTREE,
  INDEX `idx_platform_id`(`platform_openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_user_platform
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_user_platform`;
CREATE TABLE `qk_system_user_platform`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号id',
  `nickname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `gender` enum('male','female','unknow') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unknow' COMMENT '性别',
  `platform_openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '平台id 如微信 openid',
  `platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户所属平台标识 facebook,google,wechat,qq,weibo,twitter,weapp',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'h5密码',
  `unionid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信unionid',
  `subscribe` tinyint(1) NOT NULL DEFAULT 0 COMMENT '微信是否关注',
  `created_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建日期',
  `updated_at` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新日期',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_platform`(`platform`) USING BTREE,
  INDEX `idx_platform_id`(`platform_openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '第三方用户信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qk_system_user_token
-- ----------------------------
DROP TABLE IF EXISTS `qk_system_user_token`;
CREATE TABLE `qk_system_user_token`  (
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `expire_time` int(11) NULL DEFAULT NULL COMMENT '过期时间',
  INDEX `idx_token`(`token`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '会员Token表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
