<font color='red'>重要通知：Hush Framework 已正式迁至 Github：<a href='https://github.com/jameschz/hush'>https://github.com/jameschz/hush</a>，感谢大家继续支持！</font>

**特别公告：《Android和PHP最佳实践》的读者请直接访问本书官网：http://code.google.com/p/android-php/**

## Hush Framework Description ##

Powerful and stackful web application framework for PHP (Useful for ERP)

Based on Zend Framework and Smarty template engine!

By -- James.Huang (黄隽实)

## 框架特点（Features） ##

1> ZendFramework 和 Smarty 的完美结合（MVC）

2> 优化的 ZendFramework Url Mapping 机制（比 ZF 快 N 倍）

3> 完善的 Full Stack 前后台松耦合框架结构（带调试框架）

4> 优化数据库连接，支持主从分离，分库分表策略

5> 强大的 RBAC 权限控制系统（可扩展）

6> 整合 BPM 流程管理框架（可编程）

7> 多进程，消息处理（用于 CLI）

## 框架简介（Introduction） ##

1、关于 MVC：

实际上 HF 基本上复制了 ZF 的 MVC 结构，Module 使用的是基于 Zend\_Db 的 Hush\_Db 类，
Hush\_Db 类使用的是 Zend\_Db 基本的 Adaptor，然后在上面添加了一些优化的方法，比如多行插入等，
然后把 Hush\_Debug 类嵌入其中，让用户可以很轻易的使用 Debug 控制台观测应用的所有 SQL。
而在 View 方面，HF 使用的是 Smarty 模板，这个理由就不多说了吧，然后优化了 ZF 的 URL Router 流程机制，添加了包含模糊匹配功能的 mapping 文件，速度绝对快 （可以看到上图中的 Hush App Dispatch Time 就是他的执行时间了，微秒级别的哦），让你可以更简单构建 RESTful 的网络应用。最后在 Controller 方面，HF 使用的 Hush\_Page 类，里面和 ZF 中的 Controller 基本没什么区别 Action 映射也是遵循 {ActionName}Action 规则，要说不同就是添加了单独页面的可继承机制，简单说就是如果你不想使用 URL Router 机制，你也可以方便的通过集成 Hush\_Page 类来使用其提供的简便方法。

2、关于 RBAC：

众所周知，权限控制是一个基于用户的应用系统的最核心部分，HF 的 ACL 模块 Hush\_Acl 已经实现了基于 Zend\_Acl 的 RBAC 权限管理策略，而且极易扩展，因为 HF 的后台里面已经实现了菜单权限以及更细化的权限管理，开发者只需要通过一些简单的界面操作就可以扩展 HF 的 ACL 权限控制到你的具体应用中。

3、关于 BPM：

这绝对是令人兴奋的一大亮点，HF 使用类似于 JBPM 的架构实现了可编程（自带类似 PHP 的语言 PBEL）的企业流程管理框架。目前后台的实例已经支持自定义模型，流程图，流程控制等功能，目前这方面的功能还在不断增强中。关于这部分信息，也可见 Hush Framework 的另一个子项目 PBPM (http://code.google.com/p/pbpm/)

4、关于 DEBUG：

开发过程中，免不了要调试和观测系统的运行状态，于是就出现了 Hush\_Debug 模块，此模块可以说是 HF 的重要创新之一，可以从上图看到黄色背景的部分就是 HF 的 Debug Console 了，用户可以通过 URL 中的 debug 参数（例如 ?debug=time,sql）决定需要显示的 Debug 信息，红色的信息是系统自带的，目前支持页面时间调试和 SQL 调试，当然用户可以使用 Hush\_Debug 中提供的方法操作自己的 Debug 信息。目前 Hush\_Debug 优先级包含 DEBUG、INFO、WARN、ERROR、FATAL 五个级别的 Debug 信息，可以通过 setDebugLevel 方法来设置应用可显示的 Debug 级别，另外用户还可以通过扩充 Hush\_Debug\_Writer 抽象类来实现自己的 Debug Log 记录接口。

5、关于 Full Stack：

Hush Framework 应用包括：框架类库和应用程序两个部分，而应用程序又包含：前台程序、后台程序两个部分，所以可以看到 HF 的配制文件 （位于 etc/ 目录下） 分为三个大块：global.xxx.php(ini)、frontend.xxx.php(ini) 和 backendxxx.php(ini) ，分别是全局、前台和后台的配制文件。安装的时候开发者把程序解压配制好配制文件中的 Http Server 、 Database 以及 Cache 地址就可以运行 HF 应用程序了，后台默认超级用户/密码：sa/sa，另外还可以通过 bin/ 目录下的 hush.sh(bat) 来建立 HF 必须的一些 runtime 目录并赋权，使用相当方便，关于其他的一些命令行工具比如创建模块工具等，日后可以慢慢完善。

6、关于 多进程 / 消息 处理：

本框架另包含一个多进程处理的 Hush\_Porcess 类，以及消息/消息队列管理的 Hush\_Message 类包，具体信息可以查看 Hush Framework 的另一个子项目 PMS (http://code.google.com/p/pms-framework/)

## 最佳实践（HF in Action） ##

http://blog.csdn.net/shagoo/article/details/5655539