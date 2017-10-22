import pifacedigitalio as piface
import argparse
from time import sleep

parser = argparse.ArgumentParser()
parser.add_argument("pin", help="The pin number to trigger", type=int)
parser.add_argument("time", help="Number of milliseconds to hold the pin up", type=int)
args = parser.parse_args()

piface.init()

piface.digital_write(args.pin, 1)
sleep(args.time / 1000)
piface.digital_write(args.pin, 0)
