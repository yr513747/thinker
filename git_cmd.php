{
	"optOut": false,
	"lastUpdateCheck": 1585073523849
}git remote add origin git@github.com:yr513747/thinker.git
ssh-keygen -t rsa -C "455644483@qq.com"
将项目克隆到本地，git clone git@github.com:yr513747/thinker.git /top-think/think.git或git clone https://github.com/top-think/think tp5
git init 
git config --global remote.origin.url "git@github.com:yr513747/thinker.git"
git config ––global user.name "yr513747"
git config ––global user.email "455644483@qq.com"
git push –u origin master
git push origin master
git pull --rebase origin master
ssh -T git@github.com//测试链接
git config --list
git config  remote.origin.url "git@github.com:yr513747/thinker.git"
git push ––help
git config --unset remote.origin.url
git init //把这个目录变成Git可以管理的仓库

　　git add README.md //文件添加到仓库

　　git add . //不但可以跟单一文件，还可以跟通配符，更可以跟目录。一个点就把当前目录下所有未追踪的文件全部add了 

　　git commit -m "first commit" //把文件提交到仓库　　git remote add origin git@github.com:wangjiax9/practice.git //关联远程仓库

　　git push -u origin master //把本地库的所有内容推送到远程库上
首先，操作之前一定要看清分支！！

其次，提交代码之前一定要先更新代码！！

git branch        -----查看当前分支

git pull             -----更新代码到本地 
1、在本地修改相应文件（或者文件新旧替换）

2、git add **/**/文件名    （文件修改路径）
2、git rm **/**/文件名    （文件修改路径）

（注意路径要写对）

3、git status         ----查看当前本地文件信息

4、 git commit -m "更改信息备注" delete  。。。
git commit -m "delete" 
git commit -m "add" 
git commit -m "edit" 
git commit -m "first commit" 

5、git push               --------提交代码到当前分支
git add composer.json
git add vendor .
git rm -r --cached tests
git commit -m '删除目录'   
$ git --help                                      # 帮助命令

 

$ git pull origin master                    # 将远程仓库里面的项目拉下来

$ dir                                                # 查看有哪些文件夹

$ git rm -r --cached .idea             # 删除.idea文件夹
$ git commit -m '删除.idea'        # 提交,添加操作说明

git push -u origin master               # 将本次更改更新到github项目上去
克隆指定分支代码git clone git@github.com:yr513747/framework.git
克隆指定分支代码  git checkout master 
git clone https://github.com/top-think/framework.git
//清空git缓存 git rm -r --cached .