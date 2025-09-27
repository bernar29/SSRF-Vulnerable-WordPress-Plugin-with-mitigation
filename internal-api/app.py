from flask import Flask, jsonify
import os, time

 # initiate the flask instance
app = Flask(__name__)
 # get the 'default secret' which doesn't happen to be in the env vars
SECRET = os.environ.get("INTERNAL_SECRET", "flag{DEFAULT_SECRET}")

 # when client url 'localhost:<port>/secret' receives http GET
@app.get("/secret")
def secret():
	return jsonify({		# return a json obj
		"internal": True,
		"secret": SECRET,	# use the default secret
		"note": "Internal service data - not meant for direct external exposure."
	})

@app.get("/health")
def health():
	return jsonify({ "Status": "ok" })

@app.get("/slow")
def slow():
	time.sleep(5)
	return jsonify({ "Status": "slow_ok" })

if __name__ == "__main__":
	app.run(host="0.0.0.0", port=5000)
