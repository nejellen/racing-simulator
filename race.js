var ts = 0;

var interval;

var viewport_width = 1500;
var viewport_height = 1000;

// units feet
var world_width = 3000;
var world_height = 2000;
var track_width = 80;
var track_left_center_x = 840;
var track_right_center_x = 2160;
var track_left_center_y = 840;
var track_right_center_y = 840;
var track_radius = 420.169;
var lane_count = 20;
var lane_width = 4;

var speed;
var follow;
var paused = false;

function update_speed() {
    speed = parseInt(document.getElementById('speed').value);
}

function toggle_pause() {
    if (paused) {
        interval = window.setInterval(function() { draw2(); }, 100);
        document.getElementById('playpause').innerHTML = "Pause";
        paused = false;
    } else {
        window.clearInterval(interval);
        document.getElementById('playpause').innerHTML = "Unpause";
        paused = true;
    }
}

function setup_follow() {
    f = document.getElementById('follow');

    for (var i = 0; i < data[0].length; i++) {
        var opt = document.createElement('option');
        opt.value = i+1;
        opt.innerHTML = "Horse "+(i+1);
        f.appendChild(opt);
    }
    
    follow = 1;
}
function update_follow() {
    follow = parseInt(document.getElementById('follow').value);
}

function rewind() {
    ts -= (10 * speed);
    if (ts < 0) {
        ts = 0;
    }
    draw2();
}

function start() {
    update_speed();
    setup_follow();
	interval = window.setInterval(function() {draw2()}, 100);
}

function done() {
	window.clearInterval(interval);
}

function calc_theta(pos) {
	var offset;
	if (pos < 6178) {
		offset = 2218;
	} else {
		offset = 6178;
	}
	return (Math.PI/2.0) + (((pos-offset)/1742.0) * Math.PI);
}

function lane_offset(lane) {
	return -1*Math.floor((track_width/lane_count))*(lane - 0.5) + track_width/2;
}

function x_coordinate(pos,viewport_width,viewport_height,lane) {
	// convert pos in feet to pixels
	// TODO: make a function that converts from feet to pixels
	return pos * (viewport_width / 7920);


	/*pos += 0;

	pos %= 7920;

	straight_segment_length_ft = 2218;
	straight_segment_length_px = viewport_width/2;

	straight_segment_x_scale_factor = straight_segment_length_px/straight_segment_length_ft;

	if (pos <= 2218) {
		return viewport_width * 0.75 - (straight_segment_x_scale_factor * pos);
	} else if (2218 < pos && pos <= 3960) {
		return (((Math.cos(calc_theta(pos)))) * ((viewport_width * 0.125) + lane_offset(lane))) + viewport_width * 0.25;
	} else if (3960 < pos && pos <= 6178) {
		pos = pos - 3960;
		return viewport_width * 0.25 + (straight_segment_x_scale_factor * pos);
	} else {
		return -(((Math.cos(calc_theta(pos)))) * ((viewport_width * 0.125) + lane_offset(lane))) + viewport_width * 0.75;
	}*/
}

function y_coordinate(pos,viewport_width,viewport_height,lane) {
	// convert pos in feet to pixels
	// TODO: make a function that converts from feet to pixels
	return pos * (viewport_height / 3960);

	/*pos += 0;

	pos %= 7920;

	if (pos <= 2218) {
		return (viewport_height * 0.25) - lane_offset(lane);
	} else if (2218 < pos && pos <= 3960) {
		return -(((Math.sin(calc_theta(pos)))) * ((viewport_width * 0.125) + lane_offset(lane))) + viewport_width * 0.25;
	} else if (3960 < pos && pos <= 6178) {
		return viewport_height * 0.75 + lane_offset(lane);
	} else {
		return (((Math.sin(calc_theta(pos)))) * ((viewport_width * 0.125) + lane_offset(lane))) + viewport_width * 0.25;
	}*/
}

function draw2() {
	var canvas = document.getElementById('the_canvas');
	var ctx = canvas.getContext('2d');

	var horses = data[ts];

	var horse_to_follow = horses[0];

	for (i = 0; i < horses.length; i++) {
		if (horses[i].pp == follow) {
			horse_to_follow = horses[i];
			break;
		}
	}

	//var x_trans = (-1 * x_coordinate(horse_to_follow.pos,viewport_width,viewport_height,horse_to_follow.pp) + viewport_width/8);
	//var y_trans = (-1 * y_coordinate(horse_to_follow.pos,viewport_width,viewport_height,horse_to_follow.pp) + viewport_height/8);

	var main_scale = 4;

	var x_trans = (-1 * horse_to_follow.pos_x*main_scale) + (viewport_width/2);
	var y_trans = (-1 * horse_to_follow.pos_y*main_scale) + (viewport_height/2);

	draw_track(ctx, main_scale, x_trans, y_trans);
	draw_track(ctx,0.1,0,0);

	// draw background
	/*ctx.fillStyle = "green";
	ctx.fillRect (0, 0, viewport_width, viewport_height);

	//draw track

	track_width_scale = 55 / 1200;

	var horses = data[ts];

	var x_trans = (-1 * x_coordinate(horses[0].pos) + viewport_width/4);
	var y_trans = (-1 * y_coordinate(horses[0].pos) + viewport_height/4);

	ctx.beginPath();
	ctx.moveTo(viewport_width * 0.25 + x_trans , viewport_height * 0.25 + y_trans);
	ctx.lineTo(viewport_width * 0.75 + x_trans, viewport_height * 0.25 + y_trans);
	ctx.arc(viewport_width * 0.75 + x_trans, viewport_height * 0.5 + y_trans, viewport_height * 0.25, 3*Math.PI/2, Math.PI/2, false);
	ctx.lineTo(viewport_width * 0.25 + x_trans, viewport_height * 0.75 + y_trans);
	ctx.arc(viewport_width * 0.25 + x_trans, viewport_height * 0.5 + y_trans, viewport_height * 0.25, Math.PI/2, 3*Math.PI/2, false);
	ctx.lineWidth = viewport_width * track_width_scale;
	ctx.strokeStyle = "brown";
	ctx.stroke();

	
	for (var i = 0; i < horses.length; i++) {
		var horse = horses[i];
		var pos = horse.pos;
		var x = x_coordinate(pos);
		var y = y_coordinate(pos) + i*10;

		ctx.fillStyle = "rgb(0, 0, 0)";
		ctx.fillRect (x-5 + x_trans, y-5 + y_trans, 10, 10);
	}*/

	/*var pos = data[ts][0].pos;
	var pos2 = data[ts][1].pos;

	var x1 = x_coordinate(pos);
	var y1 = y_coordinate(pos);

	var x2 = x_coordinate(pos2);
	var y2 = y_coordinate(pos2) + 10;

	ctx.fillStyle = "rgb(0, 0, 0)";
	ctx.fillRect (x1-5, y1-5, 10, 10);

	ctx.fillStyle = "rgb(0, 0, 255)";
	ctx.fillRect (x2-5, y2-5, 10, 10);*/

	ts += speed;

	if (ts >= data.length) {
		var status = document.getElementById('status_message');
		status.innerHTML = "The race is over!";
		window.clearInterval(interval);
	}
}

/**
 *
 * @param ctx
 * @param scale
 * @param x_trans
 * @param y_trans
 */
function draw_track(ctx, scale, x_trans, y_trans) {
	// draw background
	ctx.fillStyle = "rgb(51,102,0)";
	ctx.fillRect (0, 0, world_width * scale, world_height * scale);

	//draw track

	var horses = data[ts];

	ctx.beginPath();
	ctx.moveTo(track_left_center_x * scale + x_trans, (track_left_center_y - track_radius - (track_width/2)) * scale + y_trans);
	ctx.lineTo(track_right_center_x * scale + x_trans, (track_left_center_y - track_radius - (track_width/2)) * scale + y_trans);
	ctx.arc(track_right_center_x * scale + x_trans, track_right_center_y * scale + y_trans, (track_radius + (track_width/2)) * scale, 3*Math.PI/2, Math.PI/2, false);
	ctx.lineTo(track_left_center_x * scale + x_trans, (track_left_center_y + track_radius + (track_width/2)) * scale + y_trans);
	ctx.arc(track_left_center_x * scale + x_trans, track_left_center_y * scale + y_trans, (track_radius + (track_width/2)) * scale, Math.PI/2, 3*Math.PI/2, false);
	ctx.lineWidth = track_width * scale;
	ctx.strokeStyle = "rgb(142,95,63)";
	ctx.stroke();

	for (var i = 1; i < 20; i++) {
		ctx.beginPath();
		ctx.moveTo(track_left_center_x * scale + x_trans, (track_left_center_y - track_radius - i*lane_width) * scale + y_trans);
		ctx.lineTo(track_right_center_x * scale + x_trans, (track_left_center_y - track_radius - i*lane_width) * scale + y_trans);
		ctx.arc(track_right_center_x * scale + x_trans, track_right_center_y * scale + y_trans, (track_radius + i*lane_width) * scale, 3*Math.PI/2, Math.PI/2, false);
		ctx.lineTo(track_left_center_x * scale + x_trans, (track_left_center_y + track_radius + i*lane_width) * scale + y_trans);
		ctx.arc(track_left_center_x * scale + x_trans, track_left_center_y * scale + y_trans, (track_radius + i*lane_width) * scale, Math.PI/2, 3*Math.PI/2, false);
		ctx.lineWidth = 1;
		ctx.strokeStyle = "rgb(106,72,47)";
		ctx.stroke();
	}

	for (var i = 0; i < horses.length; i++) {
		var horse = horses[i];
		var x = horse.pos_x * scale + x_trans;
		var y = horse.pos_y * scale + y_trans;
		// var x = x_coordinate(pos_x,viewport_width,viewport_height, horse.pp);
		// var y = y_coordinate(pos_y,viewport_width,viewport_height, horse.pp);

		if (horse.pp == 1) {
			ctx.fillStyle = "rgb(204,21,21)"; //red
		} if (horse.pp == 2) {
			ctx.fillStyle = "rgb(255,255,255)"; //white
		} if (horse.pp == 3) {
			ctx.fillStyle = "rgb(0,0,204)"; //blue
		} if (horse.pp == 4) {
			ctx.fillStyle = "rgb(204,204,0)"; //yellow
		} if (horse.pp == 5) {
			ctx.fillStyle = "rgb(0,102,0)"; //green
		} if (horse.pp == 6) {
			ctx.fillStyle = "rgb(0,0,0)"; //black
		} if (horse.pp == 7) {
			ctx.fillStyle = "rgb(255,128,0)"; //orange
		} if (horse.pp == 8) {
			ctx.fillStyle = "rgb(255,153,204)"; //pink
		} if (horse.pp == 9) {
			ctx.fillStyle = "rgb(0,204,204)"; //turqoise
		} if (horse.pp == 10) {
			ctx.fillStyle = "rgb(102,0,204)"; //purple
		} if (horse.pp == 11) {
			ctx.fillStyle = "rgb(160,160,160)"; //gray
		} if (horse.pp == 12) {
			ctx.fillStyle = "rgb(153,255,153)"; //lime
		} if (horse.pp == 13) {
			ctx.fillStyle = "rgb(92,66,46)"; //brown
		} if (horse.pp == 14) {
			ctx.fillStyle = "rgb(128,0,0)"; //maroon
		}

		ctx.save();
		ctx.translate(x,y);
		ctx.rotate(-1*horse.dir);

		var horse_width = 3*scale;
		var horse_height = 8*scale;

		ctx.fillRect (-1 * (horse_height /2), -1 * (horse_width/2), horse_height, horse_width);

		ctx.restore();

		ctx.fillRect(horse.tx*scale + x_trans-2, horse.ty*scale + y_trans-2, 4, 4);

        ctx.fillRect(300+30*i,20,30,20);

        if (horse.pp != 3 && horse.pp != 6 && horse.pp != 10 && horse.pp < 13) {
            ctx.fillStyle = "rgb(0,0,0)"; // black
        } else {
            ctx.fillStyle = "rgb(255,255,255)"; // black
        }
        ctx.font=("16px Arial");
        ctx.fillText(horse.pp,300+30*i+8,35);

        if (scale >= 1) {
            ctx.font=(horse_width-1)+"px Arial";
            ctx.fillText(horse.pp,x+0,y+(horse_width/2)-1);
        }
    }
}
