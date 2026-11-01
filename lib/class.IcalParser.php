<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CGCalendar (c) 2008
#      by Robert Allen (akrabat) to 2008 and
#         Robert Campbell (calguy1000@cmsmadesimple.org) (2008 and beyond)
#  An addon module for CMS Made Simple to allow displaying calendars,
#  and management and display of time based events.
#
#  This module was originally forked in 2009 from the Calendar module by Robert Allen,
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
namespace CGCalendar;   // changed from namespace om
/**
 * Copyright (c) 2004-2015 Roman Ožana (http://www.omdesign.cz)
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class IcalParser {

	/** @var \DateTimeZone */
	public $timezone;

	/** @var array */
	public $data;

	/** @var array */
	public $windows_timezones = [
		'Dateline Standard Time' => 'Etc/GMT+12',
		'UTC-11' => 'Etc/GMT+11',
		'Hawaiian Standard Time' => 'Pacific/Honolulu',
		'Alaskan Standard Time' => 'America/Anchorage',
		'Pacific Standard Time (Mexico)' => 'America/Santa_Isabel',
		'Pacific Standard Time' => 'America/Los_Angeles',
		'US Mountain Standard Time' => 'America/Phoenix',
		'Mountain Standard Time (Mexico)' => 'America/Chihuahua',
		'Mountain Standard Time' => 'America/Denver',
		'Central America Standard Time' => 'America/Guatemala',
		'Central Standard Time' => 'America/Chicago',
		'Central Standard Time (Mexico)' => 'America/Mexico_City',
		'Canada Central Standard Time' => 'America/Regina',
		'SA Pacific Standard Time' => 'America/Bogota',
		'Eastern Standard Time' => 'America/New_York',
		'US Eastern Standard Time' => 'America/Indianapolis',
		'Venezuela Standard Time' => 'America/Caracas',
		'Paraguay Standard Time' => 'America/Asuncion',
		'Atlantic Standard Time' => 'America/Halifax',
		'Central Brazilian Standard Time' => 'America/Cuiaba',
		'SA Western Standard Time' => 'America/La_Paz',
		'Pacific SA Standard Time' => 'America/Santiago',
		'Newfoundland Standard Time' => 'America/St_Johns',
		'E. South America Standard Time' => 'America/Sao_Paulo',
		'Argentina Standard Time' => 'America/Buenos_Aires',
		'SA Eastern Standard Time' => 'America/Cayenne',
		'Greenland Standard Time' => 'America/Godthab',
		'Montevideo Standard Time' => 'America/Montevideo',
		'Bahia Standard Time' => 'America/Bahia',
		'UTC-02' => 'Etc/GMT+2',
		'Azores Standard Time' => 'Atlantic/Azores',
		'Cape Verde Standard Time' => 'Atlantic/Cape_Verde',
		'Morocco Standard Time' => 'Africa/Casablanca',
		'UTC' => 'Etc/GMT',
		'GMT Standard Time' => 'Europe/London',
		'Greenwich Standard Time' => 'Atlantic/Reykjavik',
		'W. Europe Standard Time' => 'Europe/Berlin',
		'Central Europe Standard Time' => 'Europe/Budapest',
		'Romance Standard Time' => 'Europe/Paris',
		'Central European Standard Time' => 'Europe/Warsaw',
		'W. Central Africa Standard Time' => 'Africa/Lagos',
		'Namibia Standard Time' => 'Africa/Windhoek',
		'GTB Standard Time' => 'Europe/Bucharest',
		'Middle East Standard Time' => 'Asia/Beirut',
		'Egypt Standard Time' => 'Africa/Cairo',
		'Syria Standard Time' => 'Asia/Damascus',
		'South Africa Standard Time' => 'Africa/Johannesburg',
		'FLE Standard Time' => 'Europe/Kiev',
		'Turkey Standard Time' => 'Europe/Istanbul',
		'Israel Standard Time' => 'Asia/Jerusalem',
		'Libya Standard Time' => 'Africa/Tripoli',
		'Jordan Standard Time' => 'Asia/Amman',
		'Arabic Standard Time' => 'Asia/Baghdad',
		'Kaliningrad Standard Time' => 'Europe/Kaliningrad',
		'Arab Standard Time' => 'Asia/Riyadh',
		'E. Africa Standard Time' => 'Africa/Nairobi',
		'Iran Standard Time' => 'Asia/Tehran',
		'Arabian Standard Time' => 'Asia/Dubai',
		'Azerbaijan Standard Time' => 'Asia/Baku',
		'Russian Standard Time' => 'Europe/Moscow',
		'Mauritius Standard Time' => 'Indian/Mauritius',
		'Georgian Standard Time' => 'Asia/Tbilisi',
		'Caucasus Standard Time' => 'Asia/Yerevan',
		'Afghanistan Standard Time' => 'Asia/Kabul',
		'West Asia Standard Time' => 'Asia/Tashkent',
		'Pakistan Standard Time' => 'Asia/Karachi',
		'India Standard Time' => 'Asia/Calcutta',
		'Sri Lanka Standard Time' => 'Asia/Colombo',
		'Nepal Standard Time' => 'Asia/Katmandu',
		'Central Asia Standard Time' => 'Asia/Almaty',
		'Bangladesh Standard Time' => 'Asia/Dhaka',
		'Ekaterinburg Standard Time' => 'Asia/Yekaterinburg',
		'Myanmar Standard Time' => 'Asia/Rangoon',
		'SE Asia Standard Time' => 'Asia/Bangkok',
		'N. Central Asia Standard Time' => 'Asia/Novosibirsk',
		'China Standard Time' => 'Asia/Shanghai',
		'North Asia Standard Time' => 'Asia/Krasnoyarsk',
		'Singapore Standard Time' => 'Asia/Singapore',
		'W. Australia Standard Time' => 'Australia/Perth',
		'Taipei Standard Time' => 'Asia/Taipei',
		'Ulaanbaatar Standard Time' => 'Asia/Ulaanbaatar',
		'North Asia East Standard Time' => 'Asia/Irkutsk',
		'Tokyo Standard Time' => 'Asia/Tokyo',
		'Korea Standard Time' => 'Asia/Seoul',
		'Cen. Australia Standard Time' => 'Australia/Adelaide',
		'AUS Central Standard Time' => 'Australia/Darwin',
		'E. Australia Standard Time' => 'Australia/Brisbane',
		'AUS Eastern Standard Time' => 'Australia/Sydney',
		'West Pacific Standard Time' => 'Pacific/Port_Moresby',
		'Tasmania Standard Time' => 'Australia/Hobart',
		'Yakutsk Standard Time' => 'Asia/Yakutsk',
		'Central Pacific Standard Time' => 'Pacific/Guadalcanal',
		'Vladivostok Standard Time' => 'Asia/Vladivostok',
		'New Zealand Standard Time' => 'Pacific/Auckland',
		'UTC+12' => 'Etc/GMT-12',
		'Fiji Standard Time' => 'Pacific/Fiji',
		'Magadan Standard Time' => 'Asia/Magadan',
		'Tonga Standard Time' => 'Pacific/Tongatapu',
		'Samoa Standard Time' => 'Pacific/Apia',
	];

	/**
	 * @param string $file
	 * @param null $callback
	 * @return array|null
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function parseFile($file, $callback = null) {
		if (!$handle = fopen($file, 'r')) {
			throw new \RuntimeException('Can\'t open file' . $file . ' for reading');
		}
		fclose($handle);

		return $this->parseString(file_get_contents($file), $callback);
	}

	/**
	 * @param string $string
	 * @param null $callback
	 * @return array|null
	 */
	public function parseString($string, $callback = null) {
		$this->data = [];

		if (!preg_match('/BEGIN:VCALENDAR/', $string)) {
			throw new \InvalidArgumentException('Invalid ICAL data format');
		}

		$counters = [];
		$section = 'VCALENDAR';

		// Replace \r\n with \n
		$string = str_replace("\r\n", "\n", $string);

		// Unfold multi-line strings
		$string = str_replace("\n ", "", $string);

		foreach (explode("\n", $string) as $row) {

			switch ($row) {
				case 'BEGIN:DAYLIGHT':
				case 'BEGIN:VALARM':
				case 'BEGIN:VTIMEZONE':
				case 'BEGIN:VFREEBUSY':
				case 'BEGIN:VJOURNAL':
				case 'BEGIN:STANDARD':
				case 'BEGIN:VTODO':
				case 'BEGIN:VEVENT':
					$section = substr($row, 6);
					$counters[$section] = isset($counters[$section]) ? $counters[$section] + 1 : 0;
					continue 2; // while
					break;
				case 'END:DAYLIGHT':
				case 'END:VALARM':
				case 'END:VTIMEZONE':
				case 'END:VFREEBUSY':
				case 'END:VJOURNAL':
				case 'END:STANDARD':
				case 'END:VEVENT':
				case 'END:VTODO':
				case 'END:VCALENDAR':
					continue 2; // while
					break;
			}

			list($key, $middle, $value) = $this->parseRow($row);


			if ($callback) {
				// call user function for processing line
				call_user_func($callback, $row, $key, $middle, $value, $section, $counters[$section]);
			} else {
				if ($section === 'VCALENDAR') {
					$this->data[$key] = $value;
				} else {
					if($key === 'ATTACH') {
						// use an array since there can be multiple attachments.  This does not
						// break the current implementation--it leaves the ATTACH key alone.
						$newKey = 'ATTACHMENTS';
						$this->data[$section][$counters[$section]][$newKey][] = $value;
					}

					$this->data[$section][$counters[$section]][$key] = $value;
				}

			}
		}

		return ($callback) ? null : $this->data;
	}

	/**
	 * @param $row
	 * @return array
	 */
	private function parseRow($row) {
		preg_match('#^([\w-]+);?(.*?):(.*)$#i', $row, $matches);

		$key = false;
		$middle = null;
		$value = null;

		if ($matches) {
			$key = $matches[1];
			$middle = $matches[2];
			$value = $matches[3];
			$timezone = null;

			if ($key === 'X-WR-TIMEZONE' || $key === 'TZID') {
				if (preg_match('#(\w+/\w+)$#i', $value, $matches)) {
					$value = $matches[1];
				}
				if (isset($this->windows_timezones[$value])) {
					$value = $this->windows_timezones[$value];
				}
				$this->timezone = new \DateTimeZone($value);
			}

			// have some middle part ?
			if ($middle && preg_match_all('#(?<key>[^=;]+)=(?<value>[^;]+)#', $middle, $matches, PREG_SET_ORDER)) {
				$middle = [];
				foreach ($matches as $match) {
					if ($match['key'] === 'TZID') {
						if (isset($this->windows_timezones[$match['value']])) {
							$match['value'] = $this->windows_timezones[$match['value']];
						}
						try {
							$middle[$match['key']] = $timezone = new \DateTimeZone($match['value']);
						} catch (\Exception $e) {
							$middle[$match['key']] = $match['value'];
						}
					}
				}
			}
		}

		// process simple dates with timezone
		if ($key === 'DTSTAMP' || $key === 'LAST-MODIFIED' || $key === 'CREATED' || $key === 'DTSTART' || $key === 'DTEND') {
			try {
				$value = new \DateTime($value, ($timezone ?: $this->timezone));
			} catch (\Exception $e) {
				$value = null;
			}
		}

		if ($key === 'RRULE' && preg_match_all('#(?<key>[^=;]+)=(?<value>[^;]+)#', $value, $matches, PREG_SET_ORDER)) {
			$middle = null;
			$value = [];
			foreach ($matches as $match) {
				$value[$match['key']] = $match['value'];
			}
		}

		//split by comma, escape \,
		if ($key === 'CATEGORIES') {
			$value = preg_split('/(?<![^\\\\]\\\\),/', $value);
		}

		//implement 4.3.11 Text ESCAPED-CHAR
		$text_properties = array(
			'CALSCALE', 'METHOD', 'PRODID', 'VERSION', 'CATEGORIES', 'CLASS', 'COMMENT', 'DESCRIPTION'
		, 'LOCATION', 'RESOURCES', 'STATUS', 'SUMMARY', 'TRANSP', 'TZID', 'TZNAME', 'CONTACT', 'RELATED-TO', 'UID'
		, 'ACTION', 'REQUEST-STATUS'
		);
		if (in_array($key, $text_properties) || strpos($key, 'X-') === 0) {
			if (is_array($value)) {
				foreach ($value as &$var) {
					$var = strtr($var, ['\\\\' => '\\', '\\N' => "\n", '\\n' => "\n", '\\;' => ';', '\\,' => ',']);
				}
			} else {
				$value = strtr($value, ['\\\\' => '\\', '\\N' => "\n", '\\n' => "\n", '\\;' => ';', '\\,' => ',']);
			}
		}

		return [$key, $middle, $value];
	}

	/**
	 * @return array
	 */
	public function getEvents() {
		return isset($this->data['VEVENT']) ? $this->data['VEVENT'] : [];
	}

	/**
	 * @return array
	 */
	public function getAlarms() {
		return isset($this->data['VALARM']) ? $this->data['VALARM'] : [];
	}

	/**
	 * @return array
	 */
	public function getTimezones() {
		return isset($this->data['VTIMEZONE']) ? $this->data['VTIMEZONE'] : [];
	}

	/**
	 * Return sorted eventlist as array or false if calenar is empty
	 *
	 * @return array|boolean
	 */
	public function getSortedEvents() {
		if ($events = $this->getEvents()) {
			usort(
				$events, function ($a, $b) {
				return $a['DTSTART'] > $b['DTSTART'];
			}
			);
			return $events;
		}
		return [];
	}

	/**
	 * @return array
	 */
	public function getReverseSortedEvents() {
		if ($events = $this->getEvents()) {
			usort(
				$events, function ($a, $b) {
				return $a['DTSTART'] < $b['DTSTART'];
			}
			);
			return $events;
		}
		return [];
	}
}
