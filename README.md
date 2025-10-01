### Build & USE

1. 安装 **[Docker](https://www.docker.com/)** ，开启服务  
	Install Docker and start the service.

2. 进入相应题目文件夹下的 `docker` 文件夹下（例如： `./example/docker/` ），输入以下代码构建题目镜像  
	Enter the docker folder under the corresponding problem folder (for example: ./example/docker/), and enter the following code to build the problem image:

   ```bash
   docker build -t {name}:{tag} .
   # {name} 镜像名 | Image name
   # {tag}	版本号 | Version number
   ```

3. 参见 **[GZCTF](https://github.com/GZTimeWalker/GZCTF)** 的使用，开启题目  
	Refer to the usage of GZCTF to start the problem.

### Notice


1. **第三方内容说明** | Third-Party Content Statement
   本仓库中可能包含引用或使用的第三方软件、素材、代码片段及其他内容（如开源组件、公开文档、社区资源等），其版权归原作者或合法持有人所有。  
   	This repository may contain third-party software, materials, code snippets, and other content (such as open-source components, public documents, community resources, etc.) that are referenced or used. The copyright of such content belongs to the original authors or legitimate holders.   
   本人不对该类内容主张任何版权，仅为学习研究目的进行整理、备份或合理使用，且未将其用于任何商业用途（包括但不限于销售、付费服务、广告推广等）。  
   	I do not claim any copyright over such content. They are only collated, backed up, or used reasonably for learning and research purposes, and have not been used for any commercial purposes (including but not limited to sales, paid services, advertising promotions, etc.). 
2. **原创内容许可** | License for Original Content
   本仓库中由本人创作的原创内容（如原创整理的文档、自定义配置文件、修改优化的代码等），适用 [ MIT License ](./LICENSE) 进行授权。
   	The original content created by me in this repository (such as originally collated documents, custom configuration files, modified and optimized code, etc.) is licensed under the MIT License.
3. **免责提示** | Disclaimer
   - 若第三方内容的权利人认为本仓库的使用行为侵犯其合法权益，请联系本人（邮箱：schapter[@]duck[.]com），本人将在核实后24小时内删除相关内容。  
	If the right holder of third-party content believes that the use of this repository infringes their legitimate rights and interests, please contact me (email: schapter[@]duck[.]com). I will delete the relevant content within 24 hours after verification.
   - 本仓库的所有内容均仅供学习交流使用，使用者应自行确保其使用行为符合相关法律法规及第三方许可要求。  
	All content in this repository is for learning and exchange purposes only. Users shall ensure that their use complies with relevant laws, regulations, and third-party licensing requirements.
