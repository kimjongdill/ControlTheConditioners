#!/usr/bin/python
from Adafruit_MotorHAT import Adafruit_MotorHAT, Adafruit_DCMotor

import time
import atexit
import tweepy


def tweetBack(status, api, direction):
	
	poster = status[0].user
	post = ""
	if direction == "":
		post = "@" + poster.screen_name + " You know I don't speak Spanish!"
	
	if direction == "r":
		post = "@" + poster.screen_name + " you spin me right round, baby, right round!"
	if direction == "l":
		post = "@" + poster.screen_name + " To the left, to the left, everything you own in a box to the left!"

	if direction == "f":
		post = "@" + poster.screen_name + " Choo Choo Muttthafuckkah!"

	if direction == "b":
		post = "@" + poster.screen_name + " Who is you playin' wit? back that azz up!"

	print(post)
	try:
		api.update_status(post)
	
	except:
		print("don't tweet twice")

	return

def mineTweetText(rawText):
	#build a corpus
	forwardWords = {"forward", "ahead", "straight", "go", "onward"}
	backwardWords = {"backward", "back", "backwards", "reverse", "away", "retreat"} 	
	leftWords = {"counterclockwise", "left", "port"}
	rightWords = {"clockwise", "right", "starboard"} 

	
	
	#variables for command to robot
	direction = ""
	
	#Mine Tweet for Direction
	for word in forwardWords:
		if word in tweetText:
			direction ="f"
	for word in backwardWords:
		if word in tweetText:
			direction = "b"
	for word in rightWords:
		if word in tweetText:
			direction = "r"
	for word in leftWords:
		if word in tweetText:
			direction = "l"

	return direction
		

def twitterConnect():
	key = "Your Key"
	secret = "Your Secret"
	accessTok = "Your Access Token"
	accessSec = "Your access secret"
	ownerID = "Your owner ID"
	owner = "RealShittyRobot"	
	
	auth = tweepy.OAuthHandler(key, secret)
	auth.set_access_token(accessTok, accessSec)
	api = tweepy.API(auth)
	
	return api

def readTwitter(api):

	search_text = "@RealShittyRobot"

	status = api.search(search_text, rpp = 1)

	return status 

def makeMove(mh, direction, speed, moveTime):
    speed = int(speed)
    moveTime = float(moveTime)
    print("direction", direction, "speed ", speed, "move time", moveTime)
    frontLeft = mh.getMotor(1)
    frontRight = mh.getMotor(2)
    rearRight = mh.getMotor(3)
    rearLeft = mh.getMotor(4)
    
    
    if direction == "f":
        frontRight.run(Adafruit_MotorHAT.FORWARD)
        frontLeft.run(Adafruit_MotorHAT.FORWARD)
        rearRight.run(Adafruit_MotorHAT.FORWARD)
        rearLeft.run(Adafruit_MotorHAT.FORWARD)

    if direction == "b":
        frontRight.run(Adafruit_MotorHAT.BACKWARD)
        frontLeft.run(Adafruit_MotorHAT.BACKWARD)
        rearRight.run(Adafruit_MotorHAT.BACKWARD)
        rearLeft.run(Adafruit_MotorHAT.BACKWARD)

    if direction == "l":
        frontRight.run(Adafruit_MotorHAT.FORWARD)
        frontLeft.run(Adafruit_MotorHAT.BACKWARD)
        rearRight.run(Adafruit_MotorHAT.FORWARD)
        rearLeft.run(Adafruit_MotorHAT.BACKWARD)

    if direction == "r":
        frontRight.run(Adafruit_MotorHAT.BACKWARD)
        frontLeft.run(Adafruit_MotorHAT.FORWARD)
        rearRight.run(Adafruit_MotorHAT.BACKWARD)
        rearLeft.run(Adafruit_MotorHAT.FORWARD)

    frontLeft.setSpeed(speed)
    frontRight.setSpeed(speed)
    rearRight.setSpeed(speed)
    rearLeft.setSpeed(speed)

    time.sleep(moveTime)
    turnOffMotors(mh)
    return


# recommended for auto-disabling motors on shutdown!
def turnOffMotors(mh):
    mh.getMotor(1).run(Adafruit_MotorHAT.RELEASE)
    mh.getMotor(2).run(Adafruit_MotorHAT.RELEASE)
    mh.getMotor(3).run(Adafruit_MotorHAT.RELEASE)
    mh.getMotor(4).run(Adafruit_MotorHAT.RELEASE)

def convertSpeed(speed):
    speed = float(speed)
    speed = speed / 100 * 255.0
    return int(speed)

def moveTheRobot(direction, moveSpeed, moveTime):
    runTime = moveTime
    speed = moveSpeed

    #while True:
    mh = Adafruit_MotorHAT(addr=0x60)
	
       # direction = raw_input("Select Direction f, b, l, r or q to quit: ")
       # if direction == "q":
       #     break

       # runTime = raw_input("For how long?: ")
       # speed = raw_input("How Fast 0 - 100 %?: ")
       #speed = convertSpeed(speed)

    makeMove(mh, direction, speed, runTime)
	

if __name__ == "__main__":
	lastID = 0

	# Prompt for movement parameters
#	moveTime = raw_input("Enter the time for the robot to move: ")
#	moveSpeed = raw_input("Enter the desired speed of the robot %: ")
#	moveSpeed = convertSpeed(moveSpeed)
	moveTime = 1
	moveSpeed = 120
	
	

	time.sleep(15)
	api = twitterConnect()
		
	while(True):
		status = readTwitter(api)
		if status[0].id == lastID:
			print("no new tweets")
			time.sleep(10)
			continue
		
		tweetText = status[0].text
		tweetText = tweetText.lower()
		lastID = status[0].id
	 	
		print(tweetText)	
		direction = mineTweetText(tweetText)
		moveTheRobot(direction, moveSpeed, moveTime)
		tweetBack(status, api, direction)

