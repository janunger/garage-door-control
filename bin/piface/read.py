import pifacedigitalio as piface
import argparse

parser = argparse.ArgumentParser()
parser.add_argument("pin", type=int)
args = parser.parse_args()

piface.init()

print(piface.digital_read(args.pin))
