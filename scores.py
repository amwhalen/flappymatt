import re

regex = re.compile("\[(?P<datetime>.*?)\] \[(?P<score>[0-9]+?)\] \[(?P<ip>.*?)\]")
scores = []
top_score = 0
ips = []
players = 0
with open("scores.log") as f:
    for line in f:
        match = regex.match(line)
        score = {}
        if match:
            score['datetime'] = match.group("datetime")
            score['score'] = int(match.group("score"))
            score['ip'] = match.group("ip")
            if score['score'] > top_score:
                top_score = score['score']
            if score['ip'] not in ips:
                ips.append(score['ip'])
                players = players + 1
            scores.append(score)

print "Games: %d" % len(scores)
print "Top Score: %d" % top_score
print "Players: %d" % players