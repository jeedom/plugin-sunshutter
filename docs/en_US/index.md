# Shutter Management Plugin

# Description

This plugin makes it easier to manage the position of your shutters according to the position of the sun. This plugin works completely locally and does not require an external connection.

You can find [here](https://www.jeedom.com/blog/?p=4310) an article showing an example configuration of the plugin.

# Plugins configuration

Nothing special here just to install and activate the plugin.

## How it works ?

The plugin will adjust the position of your shutters relative to sun positions (Azimuth and Altitude) depending on condition.

# Configuration of the shutters

The configuration is broken down into several tabs :

## Equipement

You will find in the first tab all the configuration of your equipment :

- **Name of equipment** : name of equipment.
- **Parent object** : indicates the parent object to which the equipment belongs.
- **Category** : allows you to choose the category of your equipment.
- **Activate** : makes your equipment active.
- **Visible** : makes your equipment visible on the dashboard.

## Configuration

### Configuration

- **Verification** : frequency of checking the conditions and position of the flaps.
- **Regain control** : prohibits the shutter management system from changing its position if it has been moved manually. Example : the system closes the shutter, you open it, it will no longer be touched until the "Resume management" command is triggered or if the time to take over control has passed.
- **Latitude** : the latitude of your shutter / house.
- **Longitude** : the longitude of your shutter / house.
- **Altitude** : the height of your shutter / house.
- **Shutter state** : command indicating the current position of the shutter.
- **Shutter position** : control for positioning the flap.
- **Refresh shutter position (optional)** : command to refresh the position of the shutter.
- **Maximum time for one trip** : time to make a full movement (up and down or up and down) in seconds.

## Condition

- **Condition for action** : if this condition is not true, the plugin will not modify the position of the pane.
- **Mode change cancels pending suspensions** : if checked then a change of mode of the shutter returns it to automatic management.
- **Immediate actions are systematic and priority** : if checked then the immediate actions are executed even if it is suspended and without taking into account the order of the conditions.

The conditions table allows you to specify specific positioning conditions, which takes hold of the flap position table :
- **Position** : if the condition is true, the position of the shutter.
- **Fashion** : the condition only works if the shutter is in this mode (you can put several separated by commas ``,``). If this field is not filled then the condition will be tested whatever the mode.

>**Important**
>
>We are talking about the shutter mode here, it HAS NOTHING TO SEE with the mode plugin

- **Immediate action** : acts immediately as soon as the condition is true (therefore does not wait for the verification cron).
- **To suspend** : if the condition is true, it suspends the automatic management of the shutter.
- **Condition** : your condition.
- **Comment** : free fields for comments.

## Positionnement

- **% opening** : the% when the shutter is open.
- **% closing** : the% when the shutter is closed.
- **Default action** : the default action if no condition and position is valid.

This is where you will be able to manage the positioning of the shutter according to the position of the sun.

- **Azimuth** : sun position angle.
- **Elevation** : angle of height of the sun.
- **Position** : position of the shutter to take if the sun is in the Azimuth and elevation limits.
- **Condition** : additional condition to satisfy for the shutter to take this position (can be empty).
- **Comment** : free fields for comments.

>**TRICK**
>
>Little tip the site [suncalc.org](https://www.suncalc.org) allows, once your address entered, to see the position of the sun (and therefore the angles Azimuth and elevation) according to the hours of the day (just drag the small sun at the top).

## Planning

Here you can see the shutter positioning plans made in the Agenda planning.

## Commandes

- **Sun azimuth** : current azimuth angle of the sun.
- **Sun rise** : current elevation angle of the sun.
- **Force action** : forces the shutter position to be calculated according to the position of the sun and the conditions and applies the result to it regardless of the management state (paused or not).
- **Last Position** : last position requested from the shutter by the plugin.
- **Management status** : management status (suspended or not).
- **Resume** : forces the management to be returned to automatic mode (note that it is this command that must be launched to switch back to automatic management if you have modified the position of your shutter manually and checked the "Do not take back control").
- **To suspend** : suspends automatic shutter positioning.
- **Refresh** : update the values of the "Sun azimuth" and "Sun elevation" commands".
- **Fashion** : current shutter mode.

You can add "mode" commands, the command name will be the mode name.

# Panel

The plugin has a management panel for desktop and mobile. To activate it, simply go to Plugins → Plugins management, click on the pane management plugin and at the bottom right, check the boxes to display the desktop and mobile panels.
