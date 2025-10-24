from flask import Flask, render_template, request, jsonify
import os, random, time, threading, re
from datetime import datetime
from subprocess import Popen, PIPE

app = Flask(__name__)

USER_HOME = "/home/ctfuser"
VIRTUAL_ROUTE = []
FLAG_FILE = f"{USER_HOME}/flag.txt"
ABNORMAL_IP = f"10.0.0.{random.randint(20,250)}"
BANNED_IPS = []

traffic = []
packet_id = 0

os.makedirs(USER_HOME, exist_ok=True)
os.makedirs("/app", exist_ok=True)
os.chmod("/app", 0o700)

try:
    Popen(["useradd","-m","ctfuser","-u","1000","-d",USER_HOME,"-s","/bin/bash"], stdout=PIPE, stderr=PIPE)
except Exception: pass

def generate_packet():
    global packet_id
    while True:
        packet_id += 1
        ts = datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f")[:-3]
        is_abnormal = random.random()<0.3
        src_ip = ABNORMAL_IP if is_abnormal else random.choice(["192.168.1.20","192.168.1.21","10.0.0.10","172.17.0.3"])
        dst_ip = "172.17.0.2"
        proto = random.choice(["TCP","UDP","ICMP"])

        if proto=="ICMP":
            port="N/A"
            payload=f"ICMP echo request (size:{random.randint(64,128)} bytes)"
        else:
            if is_abnormal:
                port=random.choice([22,3389,5900,80])
                if port==22:
                    payload=f"SSH brute-force attempt {random.randint(1,60)} (user: 'admin', password: '123456')"
                elif port==80:
                    payload=(
                        "GET /index.php HTTP/1.1\r\n"
                        "Host: 172.17.0.2:80\r\n"
                        "User-Agent: Mozilla/5.0\r\n"
                        "Accept: text/html\r\n"
                        f"Cookie: language=php://filter/read=convert.base64-encode/resource=/var/www/html/flag\r\n"
                        "Connection: keep-alive\r\n\r\n"
                    )
                else:
                    payload=f"Large data transfer (size:{random.randint(1024*1024,10*1024*1024)} bytes)"
            else:
                port=random.choice([80,443,53,8080])
                if port in [80,443]:
                    paths=["/index.html","/api/v1/status","/static/css/main.css"]
                    payload=f"GET {random.choice(paths)} HTTP/1.1\r\nHost: 172.17.0.2:{port}\r\nUser-Agent: Mozilla/5.0\r\n\r\n"
                else:
                    payload=f"Normal {proto} traffic (size:{random.randint(64,512)} bytes)"

        summary=f"[{ts}] {proto} {src_ip}:{port} -> {dst_ip}:{port}"
        detail=(
            f"Packet ID:{packet_id}\n"
            f"Timestamp:{ts}\n"
            f"Source IP:{src_ip}\n"
            f"Destination IP:{dst_ip}\n"
            f"Protocol:{proto}\n"
            f"Port:{port}\n"
            f"Payload:{payload}\n"
            f"TTL:{random.randint(64,128)}\n"
            f"Length:{len(str(payload))+30} bytes"
        )
        traffic.append({"id":packet_id,"summary":summary,"detail":detail})
        if len(traffic)>100: traffic.pop(0)
        time.sleep(2)

threading.Thread(target=generate_packet,daemon=True).start()

@app.route("/")
def index(): return render_template("index.html")

@app.route("/get_traffic")
def get_traffic(): return jsonify(traffic)

@app.route("/get_route")
def get_route():
    try:
        proc=Popen("ip route", shell=True, stdout=PIPE, stderr=PIPE, text=True, preexec_fn=lambda: os.setuid(1000))
        real_route=proc.communicate()[0]
        virtual_route="\n".join([f"blackhole {ip}" if isinstance(ip,str) else f"blackhole {ip}" for ip in VIRTUAL_ROUTE])
        return real_route+virtual_route+"\n"
    except Exception: return "Error: Failed to get route table\n"

@app.route("/exec",methods=["POST"])
def exec_cmd():
    cmd=request.form.get("cmd","").strip()
    if not cmd: return "\n"

    # 封禁异常IP
    match_blackhole=re.fullmatch(r"ip route add blackhole (\d+\.\d+\.\d+\.\d+)", cmd)
    match_static=re.fullmatch(r"ip route-static (\d+\.\d+\.\d+\.\d+) 255\.255\.255\.255 (\S+)", cmd)

    if match_blackhole:
        ip=match_blackhole.group(1)
        if ip==ABNORMAL_IP and ip not in BANNED_IPS:
            VIRTUAL_ROUTE.append(ip)
            BANNED_IPS.append(ip)
            with open(FLAG_FILE,"w") as f:
                f.write(os.environ.get("GZCTF_FLAG",f"flag{{banned_{ABNORMAL_IP}_success}}"))
        elif ip!=ABNORMAL_IP:
            VIRTUAL_ROUTE.append(ip)
        return "\n"

    if match_static:
        ip, iface=match_static.groups()
        if iface=="NULL0" and ip==ABNORMAL_IP and ip not in BANNED_IPS:
            VIRTUAL_ROUTE.append(f"{ip}/255.255.255.255 dev {iface}")
            BANNED_IPS.append(ip)
            with open(FLAG_FILE,"w") as f:
                f.write(os.environ.get("GZCTF_FLAG",f"flag{{banned_{ABNORMAL_IP}_success}}"))
        else:
            VIRTUAL_ROUTE.append(f"{ip}/255.255.255.255 dev {iface}")
        return "\n"

    if re.match(r"^ls\s+/app|^cat\s+/app|^cd\s+/app", cmd):
        return f"-bash: {cmd.split()[0]}: Permission denied (only root can access /app)\n"
    if re.fullmatch(r"echo\s+\$GZCTF_FLAG|printenv\s+GZCTF_FLAG", cmd):
        return "-bash: Permission denied\n"
    if re.fullmatch(r"cat\s+" + re.escape(FLAG_FILE), cmd):
        if os.path.exists(FLAG_FILE):
            with open(FLAG_FILE,"r") as f: return f.read()+"\n"
        else: return f"-bash: cat: {FLAG_FILE}: No such file or directory\n"

    try:
        proc=Popen(cmd, shell=True, stdout=PIPE, stderr=PIPE, text=True, cwd=USER_HOME, preexec_fn=lambda: os.setuid(1000))
        output=proc.communicate()[0]
        if cmd.strip() in ["ip route","ip route show"]:
            for ip in VIRTUAL_ROUTE: output+=f"blackhole {ip}\n"
        return output
    except Exception:
        return f"-bash: {cmd.split()[0]}: command not found\n"

if __name__=="__main__":
    app.run(host="0.0.0.0",port=5000,debug=False)
