import React, { useState, useEffect, useMemo, useCallback } from "react";
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Cell,
  PieChart,
  Pie,
  Legend,
  LineChart,
  Line
} from "recharts";
// ─── SHARED SEED DATA ─────────────────────────────────────────────────────
const SERVICES_LIST = [
  {name:"Body Scrub",                                   cat:"Body Treatments",        price:550},
  {name:"Body Scrub with Wipe Off",                     cat:"Body Treatments",        price:750},
  {name:"Signature Massage",                            cat:"Massage",                price:800},
  {name:"Swedish Massage",                              cat:"Massage",                price:550},
  {name:"Deep Tissue Massage",                          cat:"Massage",                price:850},
  {name:"Ukuchina Massage",                             cat:"Massage",                price:950},
  {name:"Pregnancy Massage",                            cat:"Massage",                price:1100},
  {name:"Ngalula Rejuvenation Massage",                 cat:"Massage",                price:1550},
  {name:"Ngalula Recovery Massage",                     cat:"Massage",                price:1250},
  {name:"Relaxation Massage",                           cat:"Massage",                price:800},
  {name:"Mini Back Massage",                            cat:"Massage",                price:350},
  {name:"Complimentary Leg Massage",                    cat:"Massage",                price:0},
  {name:"Complimentary Palm Massage",                   cat:"Massage",                price:0},
  {name:"Ngalula Custom Unwind Massage",                cat:"Massage",                price:2000},
  {name:"Couples' Body Scrub + Ngalula Custom Unwind",  cat:"Massage",                price:5500},
  {name:"Deep Cleanse Facial",                          cat:"Facials",                price:612},
  {name:"Vitamin C Facial",                             cat:"Facials",                price:612},
  {name:"Dermaplaning with Vitamin C Facial",           cat:"Facials",                price:850},
  {name:"Extraction with Vitamin C Facial",             cat:"Facials",                price:850},
  {name:"Pedicure",                                     cat:"Nails",                  price:450},
  {name:"Pedicure with Removal",                        cat:"Nails",                  price:500},
  {name:"Pedicure with Stickons",                       cat:"Nails",                  price:550},
  {name:"Gel Manicure",                                 cat:"Nails",                  price:285},
  {name:"Gel Paint Manicure",                           cat:"Nails",                  price:285},
  {name:"Men's Pedicure & Manicure",                    cat:"Nails",                  price:800},
  {name:"Couples' Pedicure & Manicure",                 cat:"Nails",                  price:1100},
  {name:"Gel Removal",                                  cat:"Nails",                  price:150},
  {name:"Lashes",                                       cat:"Lashes",                 price:350},
  {name:"Lash Refill",                                  cat:"Lashes",                 price:150},
  {name:"Lashes Full Set",                              cat:"Lashes",                 price:500},
  {name:"Lashes Premium",                               cat:"Lashes",                 price:900},
];

const SEED_THERAPISTS = [
  {id:1,name:"Grace",           role:"General Therapist",       initials:"GS",color:"#5cdb95",specialties:"Facials, Body Scrub, Lashes, Pedicure", phone:"+260 97 111 2233",email:"grace@ngalulaspa.com",   bio:"All-round therapist — facials, body treatments & lashes.", sessions:0,rating:4.9,revenue:0,active:true},
  {id:2,name:"Memory",          role:"Massage Therapist",       initials:"MM",color:"#c9a96e",specialties:"Ukuchina, Signature, Deep Tissue, Pregnancy Massage", phone:"+260 96 222 3344",email:"memory@ngalulaspa.com",bio:"Massage specialist — deep tissue, pregnancy & recovery.",sessions:0, rating:4.8,revenue:0,active:true},
  {id:3,name:"Aisha",           role:"Body & Facial Therapist", initials:"AM",color:"#d4a8ff",specialties:"Body Scrub, Massage, Facials",     phone:"+260 95 333 4455",email:"aisha@ngalulaspa.com",    bio:"Experienced in full body treatments & facials.", sessions:0, rating:4.9,revenue:0,active:true},
  {id:4,name:"Natasha Chanda",  role:"Nail Artist",             initials:"NC",color:"#ff9eb5",specialties:"Gel Manicure, Pedicure, Nail Art",   phone:"+260 96 111 2233",email:"natasha@ngalulaspa.com", bio:"Nail artistry — gel paint, stickons & extensions.",sessions:0, rating:4.7,revenue:0,active:true},
];
const THERAPIST_BRANCH_MAP = Object.fromEntries(SEED_THERAPISTS.map((t,i)=>[t.name, i<2?"woodlands":"chilanga"]));

const SEED_BOOKINGS = [
  // ── May 1 (Friday) ──
  {id:101,ref:"MAY01-ELZ",client:"Elizabeth",phone:"",email:"",service:"Body Scrub, Massage, Pedicure, Gel Manicure, Vitamin C Facial",cat:"Body Treatments",therapist:"Grace",date:"2026-05-01",time:"09:00",amount:1635,status:"completed",payment:"paid",note:"Body scrub & massage on gift card; pedicure, removal, gel manicure & vitamin c facial. (GS, AM)",source:"existing",payMethod:"cash"},
  {id:102,ref:"MAY01-PKD",client:"Precious Kasakula & Dad",phone:"",email:"",service:"Men's Pedicure & Manicure, Gel Manicure & Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-01",time:"11:00",amount:1750,status:"completed",payment:"paid",note:"(GS, AM)",source:"existing",payMethod:"airtel"},
  {id:103,ref:"MAY01-MM",client:"Mrs Precious Mweele Mannda",phone:"",email:"",service:"Ukuchina Massage",cat:"Massage",therapist:"Memory",date:"2026-05-01",time:"14:00",amount:950,status:"completed",payment:"paid",note:"(MM)",source:"existing",payMethod:"airtel"},
  // ── May 2 (Saturday) ──
  {id:104,ref:"MAY02-DJ",client:"Djenabou",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-02",time:"10:00",amount:250,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"cash"},
  // ── May 3 (Sunday) ──
  {id:105,ref:"MAY03-MK",client:"Mulemwa Kusemwa",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-03",time:"09:00",amount:300,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  {id:106,ref:"MAY03-MF",client:"Muwezwa Francinah",phone:"",email:"",service:"Pedicure, Body Scrub, Deep Cleanse Facial",cat:"Body Treatments",therapist:"Aisha",date:"2026-05-03",time:"10:30",amount:1612,status:"completed",payment:"paid",note:"HC Makeni — Airtel money & cash (AM, GS)",source:"existing",payMethod:"airtel_cash"},
  {id:107,ref:"MAY03-TM",client:"Tumbikani Museteka",phone:"",email:"",service:"Body Scrub, Deep Tissue Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-03",time:"13:00",amount:1400,status:"completed",payment:"paid",note:"(GS, MM)",source:"existing",payMethod:"airtel"},
  // ── May 7 (Thursday) ──
  {id:108,ref:"MAY07-KR",client:"Karin",phone:"",email:"",service:"Deep Cleanse Facial",cat:"Facials",therapist:"Aisha",date:"2026-05-07",time:"10:00",amount:612,status:"completed",payment:"paid",note:"Complimentary leg massage. (AM, GS)",source:"existing",payMethod:"cash"},
  // ── May 8 (Friday) ──
  {id:109,ref:"MAY08-FM",client:"Faith Mpondela",phone:"",email:"",service:"Signature Massage",cat:"Massage",therapist:"Memory",date:"2026-05-08",time:"09:00",amount:800,status:"completed",payment:"paid",note:"K400 Airtel, K400 P2cell (MM)",source:"existing",payMethod:"airtel_p2cell"},
  {id:110,ref:"MAY08-BN",client:"Binta",phone:"",email:"",service:"Body Scrub, Ngalula Recovery Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-08",time:"11:00",amount:2000,status:"completed",payment:"paid",note:"Body scrub K750 (special price), recovery massage. (MM)",source:"existing",payMethod:"airtel"},
  {id:111,ref:"MAY08-RS",client:"Regina Sakala",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Memory",date:"2026-05-08",time:"14:00",amount:750,status:"completed",payment:"paid",note:"K750 special price (closed & opened for her). (MM)",source:"existing",payMethod:"cash"},
  // ── May 9 (Saturday) ──
  {id:112,ref:"MAY09-JM",client:"Judy Mumba",phone:"",email:"",service:"Pedicure, Gel Removal, Gel Paint",cat:"Nails",therapist:"Grace",date:"2026-05-09",time:"09:00",amount:600,status:"completed",payment:"partial",note:"Paid K150 towards next treatment. (GS)",source:"existing",payMethod:""},
  {id:113,ref:"MAY09-TM",client:"Teclah Munanku",phone:"",email:"",service:"Pedicure, Pregnancy Massage",cat:"Nails",therapist:"Memory",date:"2026-05-09",time:"11:00",amount:1550,status:"completed",payment:"paid",note:"(GS, MM)",source:"existing",payMethod:"airtel"},
  {id:114,ref:"MAY09-NN",client:"Anonymous Man",phone:"",email:"",service:"Massage",cat:"Massage",therapist:"Grace",date:"2026-05-09",time:"14:00",amount:800,status:"completed",payment:"paid",note:"Wanted Grace to go have sex with him after massage. K800 cash.",source:"existing",payMethod:"cash"},
  // ── May 10 (Sunday) ──
  {id:115,ref:"MAY10-BL",client:"Bertha Lishomwa",phone:"",email:"",service:"Body Scrub, Deep Tissue Massage",cat:"Body Treatments",therapist:"Aisha",date:"2026-05-10",time:"09:00",amount:1400,status:"completed",payment:"paid",note:"(GS, AM)",source:"existing",payMethod:"airtel"},
  {id:116,ref:"MAY10-SK",client:"Shammah Kalala",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Grace",date:"2026-05-10",time:"12:00",amount:550,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 11 (Monday) ──
  {id:117,ref:"MAY11-TC",client:"Tendai Chaiwila",phone:"",email:"",service:"Body Scrub, Swedish Massage",cat:"Body Treatments",therapist:"Grace",date:"2026-05-11",time:"09:00",amount:1100,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  {id:118,ref:"MAY11-CK",client:"Chola Kaunda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-11",time:"11:30",amount:500,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 13 (Wednesday) ──
  {id:119,ref:"MAY13-NN",client:"Neno",phone:"",email:"",service:"Pedicure, Gel Removal, Body Scrub",cat:"Nails",therapist:"Grace",date:"2026-05-13",time:"10:00",amount:1100,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 14 (Thursday) ──
  {id:120,ref:"MAY14-TN",client:"Tamara Ngoma",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-14",time:"09:00",amount:450,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"ewallet"},
  {id:121,ref:"MAY14-RK",client:"Hon Roselyn Kiwala",phone:"",email:"",service:"Pedicure, Lashes",cat:"Nails",therapist:"Grace",date:"2026-05-14",time:"10:30",amount:950,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 16 (Saturday) ──
  {id:122,ref:"MAY16-KS",client:"Karen Simonde",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-16",time:"08:00",amount:450,status:"completed",payment:"paid",note:"Added K50 for coming in abruptly. (GS)",source:"existing",payMethod:"cash"},
  {id:123,ref:"MAY16-CM",client:"Clara Musonda",phone:"",email:"",service:"Pedicure with Stickons",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"09:00",amount:575,status:"completed",payment:"paid",note:"Stickons on big toes + removal. (NC)",source:"existing",payMethod:"airtel"},
  {id:124,ref:"MAY16-PS",client:"Pamela Sikana",phone:"",email:"",service:"Pedicure with Stickons, Lashes",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"10:00",amount:1450,status:"completed",payment:"paid",note:"Pedicure with stickons K550 + lashes K900. (NC)",source:"existing",payMethod:"airtel"},
  {id:125,ref:"MAY16-SC",client:"Simon Chitanda",phone:"",email:"",service:"Signature Massage",cat:"Massage",therapist:"Memory",date:"2026-05-16",time:"11:00",amount:950,status:"completed",payment:"paid",note:"Focus on back and head — added K50 tip. (MM)",source:"existing",payMethod:"airtel"},
  {id:126,ref:"MAY16-JM",client:"Jones Mpakateni",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Grace",date:"2026-05-16",time:"12:00",amount:550,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"cash"},
  {id:127,ref:"MAY16-ROS",client:"Rose (Hon Milner Mwanakampwe)",phone:"",email:"",service:"Nails, Body Scrub, Massage",cat:"Nails",therapist:"Grace",date:"2026-05-16",time:"13:00",amount:1350,status:"completed",payment:"paid",note:"Nails K550 + body scrub + massage K1,350. (GS, NC, MM)",source:"existing",payMethod:""},
  {id:128,ref:"MAY16-PM",client:"Petronella Mulenga & Husband",phone:"",email:"",service:"Couples' Pedicure & Manicure",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"15:00",amount:1350,status:"completed",payment:"paid",note:"Couples K1,100 + daughter's pedicure K250 (K100 discount). All gel paint & nails done by Natasha Chanda. Natasha got K800.",source:"existing",payMethod:"cash"},
  // ── May 17 (Sunday) ──
  {id:129,ref:"MAY17-AC",client:"Angela Chisembele",phone:"",email:"",service:"Body Scrub, Vitamin C Facial, Mini Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-17",time:"09:00",amount:1950,status:"completed",payment:"paid",note:"Paid K200 reservation fee. (GS, MM)",source:"existing",payMethod:"airtel"},
  {id:130,ref:"MAY17-KH",client:"Kahilu",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-17",time:"11:00",amount:350,status:"completed",payment:"paid",note:"K200 Airtel, K150 cash. (GS)",source:"existing",payMethod:"airtel_cash"},
  {id:131,ref:"MAY17-SB",client:"Serge Bapaga",phone:"",email:"",service:"Body Scrub, Ngalula Rejuvenation Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-17",time:"12:00",amount:2100,status:"completed",payment:"paid",note:"(GS, MM)",source:"existing",payMethod:"cash"},
  {id:132,ref:"MAY17-MK",client:"Mwaba Kaunda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-17",time:"15:00",amount:350,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 18 (Monday) ──
  {id:133,ref:"MAY18-MN",client:"Mirriam Nyirenda",phone:"",email:"",service:"Pregnancy Massage",cat:"Massage",therapist:"Memory",date:"2026-05-18",time:"10:00",amount:1100,status:"completed",payment:"paid",note:"6-7 months. Paid for by husband. (MM). K1,100 invested in Patumba.",source:"existing",payMethod:"airtel"},
  // ── May 19 (Tuesday) ──
  {id:134,ref:"MAY19-SK",client:"Sara Kalende",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Memory",date:"2026-05-19",time:"10:00",amount:550,status:"completed",payment:"paid",note:"Closed — personal use. (MM)",source:"existing",payMethod:"airtel"},
  // ── May 21 (Thursday) ──
  {id:135,ref:"MAY21-VM",client:"Victor Mungole",phone:"",email:"",service:"Deep Tissue Massage",cat:"Massage",therapist:"Grace",date:"2026-05-21",time:"09:00",amount:850,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"cash"},
  {id:136,ref:"MAY21-SS",client:"Stanslous Shabbuwa (Wife)",phone:"",email:"",service:"Body Scrub, Massage, Pedicure",cat:"Body Treatments",therapist:"Grace",date:"2026-05-21",time:"10:30",amount:2350,status:"completed",payment:"paid",note:"Paid K2,350 but wife didn't come (travelling) — pedicure gift card. (GS)",source:"existing",payMethod:"airtel"},
  // ── May 22 (Friday) ──
  {id:137,ref:"MAY22-FB",client:"Faith Banda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-22",time:"09:00",amount:350,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  {id:138,ref:"MAY22-MJS",client:"Mmaphuti Jackina Sindimba",phone:"",email:"",service:"Dermaplaning with Vitamin C Facial, Deep Cleanse Facial",cat:"Facials",therapist:"Memory",date:"2026-05-22",time:"10:00",amount:1462,status:"completed",payment:"paid",note:"Dermaplaning w/ vitamin C K850 + deep cleanse K612 (mother & daughter). (MM)",source:"existing",payMethod:"airtel"},
  // ── May 23 (Saturday) ──
  {id:139,ref:"MAY23-AM",client:"Audrey Mwape",phone:"",email:"",service:"Pedicure with Removal",cat:"Nails",therapist:"Grace",date:"2026-05-23",time:"09:00",amount:500,status:"completed",payment:"paid",note:"Didn't want to pay for removal — left bad review on Facebook. (GS)",source:"existing",payMethod:"airtel"},
  {id:140,ref:"MAY23-PK",client:"Patson Kaluwaya (Ba Mwisho)",phone:"",email:"",service:"Body Scrub, Deep Cleanse Facial, Mini Back Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-23",time:"10:00",amount:2124,status:"completed",payment:"paid",note:"Wife's body scrub K550, wife's facial K612, his facial K612, his mini back massage K350. Enjoyed — wants to buy scrubs & cream for wife. (MM, GS)",source:"existing",payMethod:"cash"},
  {id:141,ref:"MAY23-LS",client:"Liswaniso",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-23",time:"14:00",amount:350,status:"completed",payment:"paid",note:"Napsa flats. (GS)",source:"existing",payMethod:"cash"},
  // ── May 24 (Sunday) ──
  {id:142,ref:"MAY24-CN",client:"Chileshe V Nkole",phone:"",email:"",service:"Lashes, Dermaplaning with Vitamin C Facial",cat:"Facials",therapist:"Grace",date:"2026-05-24",time:"09:00",amount:1200,status:"completed",payment:"paid",note:"Lashes K350 + dermaplaning w/ vitamin C K850 (complimentary palm massage). (GS, MM)",source:"existing",payMethod:"fnb_pay2cell"},
  {id:143,ref:"MAY24-SS",client:"Stephen Simasiku & Partner",phone:"",email:"",service:"Couples' Body Scrub + Ngalula Custom Unwind",cat:"Massage",therapist:"Memory",date:"2026-05-24",time:"11:00",amount:5500,status:"completed",payment:"paid",note:"(GS, MM)",source:"existing",payMethod:"airtel"},
  // ── May 25 (Monday) ──
  {id:144,ref:"MAY25-YM",client:"Yumba Muleba",phone:"",email:"",service:"Pedicure with Removal",cat:"Nails",therapist:"Grace",date:"2026-05-25",time:"09:00",amount:500,status:"completed",payment:"paid",note:"Closed at 12 due to holiday & fatigue. (GS)",source:"existing",payMethod:"cash"},
  // ── May 26 (Tuesday) ──
  {id:145,ref:"MAY26-CV",client:"Chimuka Victor",phone:"",email:"",service:"Body Scrub, Relaxation Massage",cat:"Body Treatments",therapist:"Grace",date:"2026-05-26",time:"09:00",amount:1800,status:"completed",payment:"paid",note:"K1,600 cash + K200 Airtel. (GS)",source:"existing",payMethod:"cash_airtel"},
  {id:146,ref:"MAY26-FT",client:"Fatima",phone:"",email:"",service:"Ngalula Rejuvenation Massage",cat:"Massage",therapist:"Memory",date:"2026-05-26",time:"11:30",amount:1550,status:"completed",payment:"paid",note:"K100 late fee included. (MM)",source:"existing",payMethod:"airtel"},
  // ── May 27 (Wednesday) ──
  {id:147,ref:"MAY27-JM",client:"Janet Mundando",phone:"",email:"",service:"Dermaplaning with Vitamin C Facial, Pedicure, Body Scrub",cat:"Facials",therapist:"Memory",date:"2026-05-27",time:"09:00",amount:1512,status:"completed",payment:"paid",note:"Dermaplaning w/ vitamin C (charged deep cleanse) K612, pedicure K450, body scrub K550, K100 discount. (MM, GS)",source:"existing",payMethod:"airtel"},
  {id:148,ref:"MAY27-MN",client:"Mwandu Nachangwa",phone:"",email:"",service:"Extraction with Vitamin C Facial, Pedicure, Lashes",cat:"Facials",therapist:"Grace",date:"2026-05-27",time:"11:30",amount:1700,status:"completed",payment:"paid",note:"Extraction w/ vitamin C K850, pedicure + removal K500, lashes K350. K238 balance to be paid when lashes done. (MM, GS)",source:"existing",payMethod:"airtel"},
  // ── May 28 (Thursday) ──
  {id:149,ref:"MAY28-NM",client:"Ngosa Masuzyo",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-28",time:"09:00",amount:500,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  {id:150,ref:"MAY28-PS2",client:"Pamela Sikana",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-28",time:"10:30",amount:150,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  // ── May 29 (Friday) ──
  {id:151,ref:"MAY29-CV2",client:"Chimuka Victor",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-29",time:"10:00",amount:525,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"cash"},
  // ── May 31 (Sunday) ──
  {id:152,ref:"MAY31-KM",client:"Ketty Musukwa",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-31",time:"09:00",amount:550,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
  {id:153,ref:"MAY31-NK",client:"Natasha Kabanda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-31",time:"10:30",amount:350,status:"completed",payment:"paid",note:"(GS)",source:"existing",payMethod:"airtel"},
];

const REVENUE_DATA = [
  {day:"May 1",rev:4335},{day:"May 2",rev:250},{day:"May 3",rev:3312},{day:"May 4",rev:0},
  {day:"May 5",rev:0},{day:"May 6",rev:0},{day:"May 7",rev:612},{day:"May 8",rev:3550},
  {day:"May 9",rev:2950},{day:"May 10",rev:1950},{day:"May 11",rev:1600},{day:"May 12",rev:0},
  {day:"May 13",rev:1100},{day:"May 14",rev:1400},{day:"May 15",rev:0},{day:"May 16",rev:6675},
  {day:"May 17",rev:4750},{day:"May 18",rev:1100},{day:"May 19",rev:550},{day:"May 20",rev:0},
  {day:"May 21",rev:3200},{day:"May 22",rev:1812},{day:"May 23",rev:2974},{day:"May 24",rev:6700},
  {day:"May 25",rev:500},{day:"May 26",rev:3350},{day:"May 27",rev:3212},{day:"May 28",rev:650},
  {day:"May 29",rev:525},{day:"May 30",rev:0},{day:"May 31",rev:900},
];

const TIME_SLOTS = ["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30"];
const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const DAYS   = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
const CAT_META = {"All":{icon:"🌸",col:"#c9a96e",bg:"rgba(201,169,110,0.08)"},"Massage":{icon:"🫧",col:"#c9a96e",bg:"rgba(201,169,110,0.08)"},"Body Treatments":{icon:"🌿",col:"#5cdb95",bg:"rgba(92,219,149,0.08)"},"Facials":{icon:"✨",col:"#a78bfa",bg:"rgba(167,139,250,0.08)"},"Nails":{icon:"💅",col:"#f472b6",bg:"rgba(244,114,182,0.08)"},"Lashes":{icon:"👁️",col:"#60a5fa",bg:"rgba(96,165,250,0.08)"}};
const STATUS_META = {pending:{l:"Pending",c:"#ffb347",bg:"rgba(255,179,71,0.12)"},confirmed:{l:"Confirmed",c:"#5cdb95",bg:"rgba(92,219,149,0.12)"},in_progress:{l:"In Progress",c:"#8b9ef7",bg:"rgba(139,158,247,0.12)"},completed:{l:"Completed",c:"#c9a96e",bg:"rgba(201,169,110,0.12)"},cancelled:{l:"Cancelled",c:"#ff4d6d",bg:"rgba(255,77,109,0.12)"}};
const PAY_META   = {unpaid:{l:"Unpaid",c:"#ff4d6d"},paid:{l:"Paid ✓",c:"#5cdb95"},partial:{l:"Partial",c:"#ffb347"}};
const PAY_METHOD_META = {
  cash:{l:"Cash",c:"#5cdb95"},airtel:{l:"Airtel Money",c:"#ffb347"},airtel_money:{l:"Airtel Money",c:"#ffb347"},
  airtel_cash:{l:"Airtel + Cash",c:"#c9a96e"},airtel_p2cell:{l:"Airtel + P2Cell",c:"#8b9ef7"},
  fnb_pay2cell:{l:"FNB Pay2Cell",c:"#60a5fa"},ewallet:{l:"E-Wallet",c:"#a78bfa"},
  cash_airtel:{l:"Cash + Airtel",c:"#c9a96e"},mtn:{l:"MTN Mobile Money",c:"#ff6b6b"},
  card:{l:"Card",c:"#a78bfa"},bank_transfer:{l:"Bank Transfer",c:"#60a5fa"},
};
const COLORS_PRESET = ["#c9a96e","#d4a8ff","#5cdb95","#ff9eb5","#8b9ef7","#ffb347","#5bc8f5","#f97d6e"];
const AIRTEL_NUMBER = "0971 234 567";
const BRANCHES = {woodlands:"Woodlands",chilanga:"Chilanga"};
const SEED_PRODUCTS = [
  {id:1,name:"Spa Candle – Lavender",cat:"Candles",price:120,stock:25,branch:"woodlands"},
  {id:2,name:"Spa Candle – Rose",cat:"Candles",price:130,stock:18,branch:"woodlands"},
  {id:3,name:"Essential Oil – Eucalyptus",cat:"Oils",price:250,stock:12,branch:"woodlands"},
  {id:4,name:"Essential Oil – Tea Tree",cat:"Oils",price:220,stock:10,branch:"woodlands"},
  {id:5,name:"Body Lotion – Shea Butter",cat:"Skincare",price:180,stock:30,branch:"woodlands"},
  {id:6,name:"Body Scrub – Coconut",cat:"Skincare",price:160,stock:22,branch:"chilanga"},
  {id:7,name:"Face Mask – Charcoal",cat:"Skincare",price:95,stock:40,branch:"chilanga"},
  {id:8,name:"Face Mask – Vitamin C",cat:"Skincare",price:110,stock:35,branch:"chilanga"},
  {id:9,name:"Massage Oil – Unscented",cat:"Oils",price:200,stock:15,branch:"chilanga"},
  {id:10,name:"Bath Salts – Lavender",cat:"Bath",price:85,stock:50,branch:"chilanga"},
];
const ADMIN_USERS = [
  {id:"superadmin",name:"Super Admin",role:"superadmin",branch:"*",pass:"admin@123#!",label:"🌸 Super Admin – Full Access"},
  {id:"admin1",name:"Admin 1 – Woodlands",role:"branch_admin",branch:"woodlands",pass:"admin1$%&",label:"🌳 Woodlands Branch"},
  {id:"admin2",name:"Admin 2 – Chilanga",role:"branch_admin",branch:"chilanga",pass:"admin2*()!",label:"🌴 Chilanga Branch"},
];

const genRef = () => "NGS-" + Math.random().toString(36).slice(2,8).toUpperCase();
const genId  = () => Date.now() + Math.floor(Math.random()*9999);
const fmtDate = d => `${DAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]}`;
const getDates = () => { const out=[]; const d=new Date(); while(out.length<21){if(d.getDay()!==0)out.push(new Date(d));d.setDate(d.getDate()+1);} return out; };

// ─── SHARED TINY COMPONENTS ───────────────────────────────────────────────
const SBadge = ({s}) => { const m=STATUS_META[s]||STATUS_META.pending; return <span style={{padding:"0.17rem 0.55rem",borderRadius:"20px",fontSize:"0.64rem",fontWeight:600,background:m.bg,color:m.c,whiteSpace:"nowrap"}}>{m.l}</span>; };
const PBadge = ({s}) => { const m=PAY_META[s]||PAY_META.unpaid; return <span style={{fontSize:"0.7rem",color:m.c,fontWeight:600}}>{m.l}</span>; };
const MethodBadge = ({m}) => { if(!m) return <span style={{fontSize:"0.65rem",color:"#3a3650",fontStyle:"italic"}}>—</span>; const meta=PAY_METHOD_META[m]||{l:m,c:"#5a5060"}; return <span style={{fontSize:"0.65rem",padding:"0.12rem 0.45rem",borderRadius:"10px",background:meta.c+"18",color:meta.c,fontWeight:600,whiteSpace:"nowrap"}}>{meta.l}</span>; };

const SI = {width:"100%",background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"10px",padding:"0.62rem 0.9rem",color:"#e8d5b7",fontSize:"0.83rem",outline:"none",boxSizing:"border-box",fontFamily:"'DM Sans',sans-serif",transition:"border-color 0.2s"};
const PrimaryBtn = ({children,onClick,disabled,style={}}) => <button onClick={onClick} disabled={disabled} style={{padding:"0.72rem 1.6rem",borderRadius:"10px",border:"none",cursor:disabled?"not-allowed":"pointer",fontWeight:600,fontSize:"0.83rem",fontFamily:"'DM Sans',sans-serif",background:"linear-gradient(135deg,#c9a96e,#dfbd7c)",color:"#0d0b10",opacity:disabled?0.35:1,transition:"all 0.2s",boxShadow:"0 2px 12px rgba(201,169,110,0.15)",...style}}>{children}</button>;
const GhostBtn  = ({children,onClick,style={}}) => <button onClick={onClick} style={{padding:"0.65rem 1.2rem",borderRadius:"10px",cursor:"pointer",fontWeight:500,fontSize:"0.82rem",fontFamily:"'DM Sans',sans-serif",background:"transparent",color:"#6e6460",border:"1px solid #1e1c26",transition:"all 0.2s",...style}}>{children}</button>;
const DangerBtn = ({children,onClick}) => <button onClick={onClick} style={{padding:"0.65rem 1.2rem",borderRadius:"10px",cursor:"pointer",fontWeight:600,fontSize:"0.82rem",fontFamily:"'DM Sans',sans-serif",background:"rgba(239,68,68,0.08)",color:"#ef4444",border:"1px solid rgba(239,68,68,0.2)",transition:"all 0.2s"}}>{ children}</button>;

function FField({label,error,children}){return(<div style={{marginBottom:"0.82rem"}}><label style={{display:"block",fontSize:"0.63rem",color:"#8a7f70",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.3rem"}}>{label}</label>{children}{error&&<div style={{fontSize:"0.63rem",color:"#ff4d6d",marginTop:"0.2rem"}}>⚠ {error}</div>}</div>);}

function BaseModal({title,subtitle,onClose,children,wide=false}){return(<div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.7)",backdropFilter:"blur(8px)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:300,padding:"1rem"}}><div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"18px",padding:"2rem",width:"100%",maxWidth:wide?"700px":"460px",maxHeight:"92vh",overflowY:"auto",boxShadow:"0 40px 100px rgba(0,0,0,0.5)"}}><div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"1.4rem"}}><div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.25rem",color:"#e8d5b7",fontWeight:600}}>{title}</h3>{subtitle&&<p style={{margin:"0.2rem 0 0",fontSize:"0.7rem",color:"#5a5060"}}>{subtitle}</p>}</div><button onClick={onClose} style={{background:"none",border:"none",color:"#4a4560",fontSize:"1.5rem",cursor:"pointer",lineHeight:1,flexShrink:0,padding:"0",opacity:0.6}}>×</button></div>{children}</div></div>);}

function DeleteConfirm({what,name,onConfirm,onClose}){return(<BaseModal title="Confirm Deletion" onClose={onClose}><div style={{textAlign:"center",padding:"0.5rem 0 1.5rem"}}><div style={{fontSize:"2.5rem",marginBottom:"0.8rem"}}>🗑️</div><p style={{color:"#a89f8c",fontSize:"0.85rem",lineHeight:1.6,margin:"0 0 0.4rem"}}>Permanently delete this {what}:</p><p style={{color:"#e8d5b7",fontWeight:600,fontSize:"0.9rem",margin:"0 0 1rem"}}>{name}</p><p style={{color:"#ef4444",fontSize:"0.72rem",margin:0}}>This cannot be undone.</p></div><div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><DangerBtn onClick={onConfirm}>Delete</DangerBtn></div></BaseModal>);}

// ─── MODE SWITCHER BAR ────────────────────────────────────────────────────
function ModeSwitcher({mode,setMode,newCount,pulseAdmin}){
  return(
    <div style={{position:"fixed",top:"12px",left:"50%",transform:"translateX(-50%)",zIndex:500,display:"flex",background:"rgba(13,11,20,0.85)",backdropFilter:"blur(20px)",border:"1px solid rgba(255,255,255,0.06)",borderRadius:"40px",padding:"4px",gap:"3px",boxShadow:"0 4px 24px rgba(0,0,0,0.4)"}}>
      {[["client","👤 Book Appointment","client"],["admin","⚙️ Admin Panel","admin"]].map(([m,label,key])=>{
        const active=mode===m;
        return(
          <button key={key} onClick={()=>setMode(m)} style={{position:"relative",padding:"0.42rem 1.1rem",borderRadius:"36px",border:"none",background:active?"linear-gradient(135deg,#c9a96e,#e8c98a)":"transparent",color:active?"#0d0b10":"#5a5060",cursor:"pointer",fontWeight:active?700:400,fontSize:"0.78rem",fontFamily:"'DM Sans',sans-serif",transition:"all 0.2s",whiteSpace:"nowrap"}}>
            {label}
            {key==="admin" && newCount>0 && (
              <span style={{position:"absolute",top:"-4px",right:"-4px",background:"#ef4444",color:"#fff",borderRadius:"50%",width:"18px",height:"18px",fontSize:"0.55rem",fontWeight:700,display:"flex",alignItems:"center",justifyContent:"center",animation:pulseAdmin?"pulse 1s ease infinite":undefined,boxShadow:"0 0 8px rgba(239,68,68,0.4)"}}>{newCount}</span>
            )}
          </button>
        );
      })}
      <style>{`@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}} @keyframes spin{to{transform:rotate(360deg)}} @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}} @keyframes slideIn{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}} @keyframes pulseRing{0%,100%{opacity:0.4;transform:translate(-50%,-50%) scale(1)}50%{opacity:0.8;transform:translate(-50%,-50%) scale(1.04)}} @keyframes floatOrb{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}} @keyframes particleRise{0%{transform:translateY(0) rotate(0deg);opacity:0}10%{opacity:0.25}90%{opacity:0.25}100%{transform:translateY(-105vh) rotate(720deg);opacity:0}} @keyframes phraseSwap{0%{opacity:0;filter:blur(8px);transform:translateY(12px)}100%{opacity:1;filter:blur(0);transform:translateY(0)}} @keyframes charIn{0%{opacity:0;transform:translateY(10px) scale(0.95)}100%{opacity:1;transform:translateY(0) scale(1)}} @keyframes slideDown{0%{opacity:0;transform:translateY(-14px)}100%{opacity:1;transform:translateY(0)}} @keyframes pulseWidth{0%,100%{width:36px;opacity:1}50%{width:48px;opacity:0.5}} @keyframes glowPulse{0%,100%{box-shadow:0 10px 40px rgba(201,169,110,0.2),0 0 80px rgba(139,158,247,0.05)}50%{box-shadow:0 10px 60px rgba(201,169,110,0.35),0 0 100px rgba(139,158,247,0.15)}`}</style>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  CLIENT BOOKING FLOW
// ═══════════════════════════════════════════════════════════════════════════

function ProgressBar({step}){
  const steps=["Services","Date & Time","Therapist","Review","Account"];
  return(
    <div style={{background:"#0d0b12",borderBottom:"1px solid #16141f",padding:"0.85rem 1.5rem",overflowX:"auto"}}>
      <div style={{display:"flex",alignItems:"center",justifyContent:"center",minWidth:"340px"}}>
        {steps.map((s,i)=>{
          const n=i+1,done=step>n,active=step===n;
          return(
            <div key={s} style={{display:"flex",alignItems:"center"}}>
              <div style={{display:"flex",flexDirection:"column",alignItems:"center",gap:"0.15rem"}}>
                <div style={{width:"24px",height:"24px",borderRadius:"50%",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.68rem",fontWeight:700,background:done?"#c9a96e":active?"rgba(201,169,110,0.15)":"#1a1823",color:done?"#0d0b10":active?"#c9a96e":"#3a3650",border:active?"2px solid #c9a96e":"2px solid transparent",transition:"all 0.3s"}}>{done?"✓":n}</div>
                <span style={{fontSize:"0.58rem",color:active?"#c9a96e":done?"#6e6460":"#3a3650",whiteSpace:"nowrap",letterSpacing:"0.05em"}}>{s}</span>
              </div>
              {i<steps.length-1&&<div style={{width:"clamp(20px,4vw,44px)",height:"1px",background:done?"#c9a96e66":"#2a2633",margin:"0 3px 1rem 3px",transition:"background 0.3s"}}/>}
            </div>
          );
        })}
      </div>
    </div>
  );
}

function GiftRedeemModal({giftCards,onRedeem,onClose}){
  const [code,setCode]=useState("");const [msg,setMsg]=useState("");const [done,setDone]=useState(null);
  const found=giftCards.find(g=>g.code===code.trim().toUpperCase());
  const handle=()=>{if(!found||found.status!=="active"){setMsg("Invalid or already redeemed code");return;}if(onRedeem)onRedeem(found);setDone(found);};
  return(
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.8)",backdropFilter:"blur(12px)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:800,padding:"1rem"}}>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"18px",padding:"2rem",width:"100%",maxWidth:"380px",textAlign:"center"}}>
        {!done?<>
          <div style={{fontSize:"2rem",marginBottom:"0.5rem"}}>🎁</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.2rem",color:"#e8d5b7",fontWeight:600,marginBottom:"0.2rem"}}>Redeem Gift Card</div>
          <div style={{fontSize:"0.7rem",color:"#3a3650",marginBottom:"1rem"}}>Enter your gift card code to book your session</div>
          <input value={code} onChange={e=>setCode(e.target.value.toUpperCase())} placeholder="NGC-XXXXXX" style={{width:"100%",padding:"0.6rem 0.8rem",borderRadius:"8px",border:`1px solid ${msg?"#ef4444":"#1e1c26"}`,background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.9rem",textAlign:"center",letterSpacing:"0.15em",boxSizing:"border-box",marginBottom:"0.4rem"}}/>
          {msg&&<div style={{fontSize:"0.68rem",color:"#ef4444",marginBottom:"0.5rem"}}>✗ {msg}</div>}
          {found&&found.status==="active"&&<div style={{background:"rgba(92,219,149,0.06)",border:"1px solid rgba(92,219,149,0.15)",borderRadius:"10px",padding:"0.6rem",marginBottom:"0.7rem"}}>
            <div style={{fontSize:"0.65rem",color:"#5a5060"}}>{found.recipient?`For: ${found.recipient}`:"Gift Card"}</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.3rem",color:"#c9a96e",fontWeight:600}}>K{found.s.toLocaleString()}</div>
            <div style={{fontSize:"0.6rem",color:"#3a3650"}}>{found.services.map(s=>s.name).join(", ")}</div>
          </div>}
          <div style={{display:"flex",gap:"0.6rem",marginTop:"0.3rem"}}>
            <GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn>
            <PrimaryBtn onClick={handle} disabled={!found||found.status!=="active"} style={{flex:1}}>Redeem</PrimaryBtn>
          </div>
        </>:<>
          <div style={{fontSize:"2.5rem",marginBottom:"0.5rem"}}>🎉</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.2rem",color:"#5cdb95",fontWeight:600,marginBottom:"0.5rem"}}>Gift Card Redeemed!</div>
          <div style={{fontSize:"0.78rem",color:"#4a4560",marginBottom:"1rem",lineHeight:1.5}}>You can now book using your gift card value of <span style={{color:"#c9a96e",fontWeight:600}}>K{done.s.toLocaleString()}</span>.</div>
          <PrimaryBtn onClick={()=>{setDone(null);setCode("");setMsg("");onClose();}}>Start Booking</PrimaryBtn>
        </>}
      </div>
    </div>
  );
}

function HeroSection({onBook,heroImageUrl,onRedeemGift}){
  const [mousePos,setMousePos]=useState({x:0.5,y:0.5});
  const [phraseIdx,setPhraseIdx]=useState(0);
  const handleMove=(e)=>{const r=e.currentTarget.getBoundingClientRect();setMousePos({x:(e.clientX-r.left)/r.width,y:(e.clientY-r.top)/r.height});};
  const phrases=["Escape to Tranquility","Find Your Balance","Awaken Your Senses","Unwind & Rejuvenate"];
  useEffect(()=>{const t=setInterval(()=>setPhraseIdx(i=>(i+1)%phrases.length),3500);return ()=>clearInterval(t);},[]);
  const sp=(from,to)=>`drop-shadow(0 0 ${from}px rgba(201,169,110,${to}))`;
  const particles=useMemo(()=>Array.from({length:24},(_,i)=>({id:i,left:Math.random()*100,delay:Math.random()*8,dur:5+Math.random()*10,size:2+Math.random()*4,op:0.15+Math.random()*0.35})),[]);
  return(
    <div onMouseMove={handleMove} style={{minHeight:"100vh",display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",position:"relative",overflow:"hidden",textAlign:"center",padding:"6rem 1.5rem 2rem",background:"#08070f"}}>
      {/* BG IMAGE */}
      <div style={{position:"absolute",inset:0,backgroundImage:heroImageUrl?`url(${heroImageUrl})`:"none",backgroundSize:"contain",backgroundPosition:"center",backgroundRepeat:"no-repeat",filter:"brightness(0.3) saturate(1.15)"}}/>
      {/* GRADIENT OVERLAYS */}
      <div style={{position:"absolute",inset:0,background:"linear-gradient(180deg,rgba(8,7,15,0.3) 0%,rgba(8,7,15,0.85) 40%,rgba(8,7,15,1) 100%)"}}/>
      <div style={{position:"absolute",inset:0,background:[`radial-gradient(ellipse 80% 50% at ${50+mousePos.x*10}% ${30+mousePos.y*8}%,rgba(201,169,110,0.1),transparent 65%)`,`radial-gradient(ellipse 60% 50% at ${30-mousePos.x*12}% ${80-mousePos.y*10}%,rgba(92,219,149,0.05),transparent 55%)`,`radial-gradient(ellipse 50% 60% at ${70-mousePos.y*14}% ${20+mousePos.x*12}%,rgba(167,139,250,0.04),transparent 50%)`].join(",")}}/>
      {/* AMBIENT GLOW */}
      <div style={{position:"absolute",inset:0,backgroundImage:`radial-gradient(circle at ${50+mousePos.x*6}% ${40+mousePos.y*5}%,rgba(201,169,110,0.05) 0%,transparent 45%)`,transition:"background-image 0.6s ease"}}/>
      {/* CONCENTRIC RINGS */}
      {[540,380,260,140].map((s,i)=><div key={s} style={{position:"absolute",width:`${s}px`,height:`${s}px`,borderRadius:"50%",border:`1px solid rgba(201,169,110,${0.04+i*0.018})`,top:`calc(50% + ${(i-1.5)*8}px)`,left:`calc(50% + ${(i-1.5)*8}px)`,transform:"translate(-50%,-50%)",pointerEvents:"none",opacity:0.6,animation:`pulseRing ${4+i*0.8}s ease-in-out infinite`,animationDelay:`${i*0.3}s`}}/>)}
      {/* FLOATING ORBS */}
      <div style={{position:"absolute",top:"15%",left:"6%",width:"140px",height:"140px",borderRadius:"50%",background:"radial-gradient(circle,rgba(201,169,110,0.05),transparent 70%)",pointerEvents:"none",animation:"floatOrb 8s ease-in-out infinite"}}/>
      <div style={{position:"absolute",bottom:"20%",right:"8%",width:"180px",height:"180px",borderRadius:"50%",background:"radial-gradient(circle,rgba(92,219,149,0.035),transparent 70%)",pointerEvents:"none",animation:"floatOrb 10s ease-in-out infinite reverse"}}/>
      <div style={{position:"absolute",top:"60%",left:"75%",width:"100px",height:"100px",borderRadius:"50%",background:"radial-gradient(circle,rgba(167,139,250,0.03),transparent 70%)",pointerEvents:"none",animation:"floatOrb 7s ease-in-out infinite 2s"}}/>
      {/* PARTICLES */}
      {particles.map(p=><div key={p.id} style={{position:"absolute",left:`${p.left}%`,bottom:"-10px",width:`${p.size}px`,height:`${p.size}px`,borderRadius:"50%",background:"#c9a96e",opacity:p.op,pointerEvents:"none",animation:`particleRise ${p.dur}s linear ${p.delay}s infinite`,boxShadow:"0 0 4px rgba(201,169,110,0.3)"}}/>)}
      <div style={{position:"relative",zIndex:2,maxWidth:"600px",animation:"fadeIn 0.9s ease both"}}>
        <div style={{display:"flex",alignItems:"center",justifyContent:"center",gap:"0.6rem",marginBottom:"2rem",animation:"slideDown 0.6s ease both 0.15s"}}>
          <div style={{flex:1,height:"1px",background:"linear-gradient(90deg,transparent,rgba(201,169,110,0.25))"}}/>
          <span style={{fontSize:"0.6rem",color:"#6e6460",letterSpacing:"0.4em",textTransform:"uppercase",fontWeight:400}}>✦ Ngalula Spa ✦</span>
          <div style={{flex:1,height:"1px",background:"linear-gradient(90deg,rgba(201,169,110,0.25),transparent)"}}/>
        </div>
        <div style={{minHeight:"7rem",display:"flex",alignItems:"center",justifyContent:"center"}}>
          <h1 key={phraseIdx} style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"clamp(3rem,8vw,5.2rem)",fontWeight:600,color:"#e8d5b7",margin:"0 0 0.6rem",lineHeight:1.04,letterSpacing:"0.02em",textShadow:"0 2px 40px rgba(0,0,0,0.3)",animation:"phraseSwap 0.9s ease both",filter:sp(8,0.15)}}>
            {phrases[phraseIdx].split(" ").map((w,i)=><span key={i} style={{display:"inline-block",animation:`charIn 0.5s ease both ${i*0.1}s`,marginRight:"0.15em"}}>{w}{i<phrases[phraseIdx].split(" ").length-1?"\u00a0":""}</span>)}
          </h1>
        </div>
        <div style={{display:"flex",alignItems:"center",justifyContent:"center",gap:"0.6rem",marginBottom:"1.6rem",animation:"fadeIn 0.6s ease both 0.45s"}}>
          <div style={{width:"36px",height:"1px",background:"#c9a96e",animation:"pulseWidth 2.5s ease-in-out infinite"}}/>
          <span style={{fontFamily:"'Cormorant Garamond',serif",fontStyle:"italic",fontSize:"0.78rem",color:"#8a7f70",letterSpacing:"0.15em"}}>luxury redefined</span>
          <div style={{width:"36px",height:"1px",background:"#c9a96e",animation:"pulseWidth 2.5s ease-in-out infinite 0.5s"}}/>
        </div>
        <p style={{fontSize:"0.92rem",color:"#7a7068",lineHeight:1.9,margin:"0 auto 2.8rem",fontWeight:300,maxWidth:"460px",animation:"fadeIn 0.6s ease both 0.6s"}}>Indulge in transformative wellness experiences crafted for your body, mind &amp; spirit.</p>
        <div style={{display:"flex",gap:"1rem",justifyContent:"center",alignItems:"center",flexWrap:"wrap",animation:"fadeIn 0.6s ease both 0.75s"}}>
          <PrimaryBtn onClick={onBook} style={{padding:"1rem 3rem",fontSize:"1rem",borderRadius:"12px",boxShadow:"0 10px 40px rgba(201,169,110,0.2)",letterSpacing:"0.04em",position:"relative",overflow:"hidden",animation:"glowPulse 3s ease-in-out infinite"}}>
            <span style={{position:"relative",zIndex:1}}>Book Your Session</span>
            <span style={{position:"absolute",inset:0,background:"linear-gradient(135deg,rgba(255,255,255,0.12),transparent 50%)",pointerEvents:"none"}}/>
          </PrimaryBtn>
          <button onClick={onRedeemGift} style={{padding:"0.7rem 1.6rem",borderRadius:"12px",border:"1px solid rgba(255,158,181,0.2)",background:"rgba(255,158,181,0.06)",color:"#ff9eb5",cursor:"pointer",fontSize:"0.8rem",fontFamily:"'DM Sans',sans-serif",letterSpacing:"0.04em",transition:"all 0.2s",fontWeight:500}}>🎁 Redeem Gift Card</button>
          <span style={{fontSize:"0.72rem",color:"#4a4560",letterSpacing:"0.08em"}}>✦ Premium service</span>
        </div>
        <div style={{display:"flex",gap:"0.4rem",justifyContent:"center",marginTop:"2.8rem",flexWrap:"wrap",animation:"fadeIn 0.6s ease both 0.9s"}}>
          {[
            {l:"Massage Therapy",i:"🫧"},{l:"Facial Treatments",i:"✨"},{l:"Nail Care",i:"💅"},{l:"Body Treatments",i:"🌿"}
          ].map((f,i)=><span key={f.l} style={{display:"flex",alignItems:"center",gap:"0.3rem",padding:"0.28rem 0.75rem",borderRadius:"20px",border:"1px solid rgba(201,169,110,0.1)",color:"#4a4560",fontSize:"0.65rem",background:"rgba(255,255,255,0.02)",backdropFilter:"blur(6px)",letterSpacing:"0.03em",animation:`fadeIn 0.5s ease both ${0.9+i*0.12}s`}}><span style={{fontSize:"0.7rem"}}>{f.i}</span>{f.l}</span>)}
        </div>
      </div>
      <div style={{position:"absolute",bottom:0,left:0,right:0,height:"120px",background:"linear-gradient(0deg,rgba(8,7,15,1) 0%,transparent 100%)",pointerEvents:"none",zIndex:1}}/>
    </div>
  );
}

function ServiceSelector({cart,toggle,total,services,companions,setCompanion,removeCompanion,onNext,onBack}){
  const [catF,setCatF]=useState("All");
  const [expComp,setExpComp]=useState(null);
  const cats=Object.keys(CAT_META);
  const filtered=catF==="All"?services:services.filter(s=>s.cat===catF);
  return(
    <div style={{maxWidth:"740px",margin:"0 auto",padding:"1.8rem 1.5rem 9rem"}}>
      <div style={{display:"flex",alignItems:"center",gap:"1rem",marginBottom:"1.2rem"}}>
        <button onClick={onBack} style={{background:"none",border:"1px solid #1e1c26",borderRadius:"8px",color:"#4a4560",cursor:"pointer",fontSize:"0.9rem",padding:"0.3rem 0.6rem",lineHeight:1,fontFamily:"'DM Sans',sans-serif"}}>←</button>
        <div>
          <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:0}}>Choose Your Services</h2>
          <p style={{color:"#5a5060",fontSize:"0.8rem",margin:"0.1rem 0 0"}}>Select one or more — add a companion to any service.</p>
        </div>
      </div>
      <div style={{display:"flex",gap:"0.4rem",marginBottom:"1.2rem",flexWrap:"wrap"}}>
        {cats.map(c=>{const m=CAT_META[c],act=catF===c;return(<button key={c} onClick={()=>setCatF(c)} style={{padding:"0.28rem 0.85rem",borderRadius:"20px",border:`1px solid ${act?m.col:"#2a2633"}`,background:act?m.bg:"transparent",color:act?m.col:"#4a4560",cursor:"pointer",fontSize:"0.74rem",fontFamily:"'DM Sans',sans-serif"}}>{m.icon} {c}</button>);})}
      </div>
      <div style={{display:"flex",flexDirection:"column",gap:"0.4rem"}}>
        {filtered.map(svc=>{
          const inCart=cart.some(i=>i.name===svc.name),m=CAT_META[svc.cat];
          const cp=companions[svc.name];
          return(
            <div key={svc.name} style={{background:inCart?m.bg:"#13111a",border:`1px solid ${inCart?m.col+"55":"#1e1c26"}`,borderRadius:"11px",transition:"all 0.15s",overflow:"hidden"}}>
              <div onClick={()=>toggle(svc)} style={{display:"flex",justifyContent:"space-between",alignItems:"center",padding:"0.85rem 1rem",cursor:"pointer",gap:"0.8rem"}}>
                <div style={{flex:1,minWidth:0}}>
                  <div style={{fontWeight:500,color:inCart?"#e8d5b7":"#a89f8c",fontSize:"0.86rem",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{svc.name}</div>
                  <div style={{fontSize:"0.67rem",color:"#4a4560",marginTop:"0.15rem"}}>{m.icon} {svc.cat}</div>
                </div>
                <div style={{display:"flex",alignItems:"center",gap:"0.7rem",flexShrink:0}}>
                  {cp&&<span style={{fontSize:"0.62rem",color:"#ff9eb5",background:"rgba(255,158,181,0.1)",padding:"0.15rem 0.4rem",borderRadius:"6px"}}>+1 👤</span>}
                  <span style={{fontWeight:700,color:m.col,fontSize:"0.86rem"}}>K{svc.price.toLocaleString()}{cp?` + K${cp.price.toLocaleString()}`:""}</span>
                  <div style={{width:"22px",height:"22px",borderRadius:"6px",border:`2px solid ${inCart?m.col:"#2a2633"}`,background:inCart?m.col:"transparent",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.78rem",color:inCart?"#0d0b10":"#3a3650",fontWeight:700}}>{inCart?"✓":"+"}</div>
                </div>
              </div>
              {inCart&&<div style={{padding:"0 1rem 0.6rem"}}>
                {!cp?<button onClick={e=>{e.stopPropagation();setExpComp(expComp===svc.name?null:svc.name);}} style={{fontSize:"0.68rem",color:"#ff9eb5",background:"rgba(255,158,181,0.06)",border:"1px dashed rgba(255,158,181,0.2)",borderRadius:"7px",padding:"0.3rem 0.7rem",cursor:"pointer",fontFamily:"'DM Sans',sans-serif"}}>+ 👤 Add Companion</button>
                :<div style={{display:"flex",gap:"0.5rem",alignItems:"center",flexWrap:"wrap"}}>
                  <input value={cp.name} onChange={e=>setCompanion(svc.name,{...cp,name:e.target.value})} placeholder="Companion name" style={{flex:"1 1 100px",padding:"0.3rem 0.5rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",fontSize:"0.7rem",outline:"none",fontFamily:"'DM Sans',sans-serif",minWidth:0}}/>
                  <span style={{fontSize:"0.65rem",color:"#4a4560"}}>K</span>
                  <input type="number" min="0" value={cp.price} onChange={e=>setCompanion(svc.name,{...cp,price:+e.target.value})} style={{width:"65px",padding:"0.3rem 0.4rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",fontSize:"0.7rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
                  <button onClick={e=>{e.stopPropagation();removeCompanion(svc.name);}} style={{fontSize:"0.7rem",color:"#ef4444",background:"none",border:"none",cursor:"pointer",padding:"0.2rem 0.3rem",fontFamily:"'DM Sans',sans-serif"}}>✕</button>
                </div>}
                {expComp===svc.name&&!cp&&<div style={{display:"flex",gap:"0.5rem",alignItems:"center",marginTop:"0.4rem",flexWrap:"wrap"}}>
                  <input value="" onChange={e=>setCompanion(svc.name,{name:e.target.value,price:svc.price})} placeholder="Companion name" style={{flex:"1 1 100px",padding:"0.3rem 0.5rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",fontSize:"0.7rem",outline:"none",fontFamily:"'DM Sans',sans-serif",minWidth:0}} autoFocus/>
                  <button onClick={e=>{e.stopPropagation();setCompanion(svc.name,{name:"",price:svc.price});}} style={{fontSize:"0.65rem",color:"#5cdb95",background:"none",border:"1px solid rgba(92,219,149,0.2)",borderRadius:"6px",padding:"0.3rem 0.6rem",cursor:"pointer",fontFamily:"'DM Sans',sans-serif"}}>Add at K{svc.price}</button>
                  <button onClick={e=>{e.stopPropagation();setExpComp(null);}} style={{fontSize:"0.7rem",color:"#4a4560",background:"none",border:"none",cursor:"pointer",padding:"0.2rem 0.3rem",fontFamily:"'DM Sans',sans-serif"}}>Cancel</button>
                </div>}
              </div>}
            </div>
          );
        })}
      </div>
      <div style={{position:"fixed",bottom:0,left:0,right:0,background:"rgba(13,11,16,0.95)",backdropFilter:"blur(16px)",borderTop:"1px solid #16141f",padding:"1rem 1.5rem",display:"flex",alignItems:"center",justifyContent:"space-between",zIndex:50,gap:"1rem"}}>
        <div>
          <GhostBtn onClick={onBack} style={{padding:"0.45rem 0.9rem",fontSize:"0.75rem",borderRadius:"8px"}}>← Back</GhostBtn>
        </div>
        <div style={{textAlign:"right"}}>
          <div style={{fontSize:"0.63rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em"}}>{cart.length} service{cart.length!==1?"s":""} selected</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.5rem",color:"#c9a96e",fontWeight:600,lineHeight:1.1}}>K{total.toLocaleString()}</div>
        </div>
        <PrimaryBtn onClick={onNext} disabled={cart.length===0}>Continue →</PrimaryBtn>
      </div>
    </div>
  );
}

function DateTimePicker({dates,selDate,setSelDate,selTime,setSelTime,bookings,therapistName,onBack,onNext}){
  const takenTimes = useMemo(()=>{
    if(!selDate||!therapistName) return [];
    const d=selDate.toISOString().slice(0,10);
    return bookings.filter(b=>b.therapist===therapistName&&b.date===d&&b.status!=="cancelled").map(b=>b.time);
  },[selDate,therapistName,bookings]);

  return(
    <div style={{maxWidth:"680px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Date &amp; Time</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Availability is live — taken slots are shown in real time.</p>
      <div style={{display:"flex",gap:"0.5rem",overflowX:"auto",paddingBottom:"0.7rem",marginBottom:"1.4rem",scrollbarWidth:"none"}}>
        {dates.map((d,i)=>{
          const act=selDate&&d.toDateString()===selDate.toDateString();
          return(<div key={i} onClick={()=>{setSelDate(d);setSelTime(null);}} style={{flexShrink:0,width:"56px",padding:"0.65rem 0.3rem",borderRadius:"12px",textAlign:"center",cursor:"pointer",background:act?"linear-gradient(135deg,#c9a96e,#e8c98a)":"#13111a",border:`1px solid ${act?"#c9a96e":"#1e1c26"}`,color:act?"#0d0b10":"#5a5060",transition:"all 0.2s"}}>
            <div style={{fontSize:"0.58rem",fontWeight:700,letterSpacing:"0.08em",textTransform:"uppercase"}}>{DAYS[d.getDay()]}</div>
            <div style={{fontSize:"1.2rem",fontWeight:700,margin:"0.12rem 0",fontFamily:"'Cormorant Garamond',serif"}}>{d.getDate()}</div>
            <div style={{fontSize:"0.56rem"}}>{MONTHS[d.getMonth()]}</div>
          </div>);
        })}
      </div>
      {selDate?(
        <>
          <p style={{color:"#5a5060",fontSize:"0.76rem",marginBottom:"0.7rem"}}>Times for <span style={{color:"#c9a96e"}}>{fmtDate(selDate)}</span> — <span style={{color:"#ff4d6d"}}>red = taken by another booking</span></p>
          <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(72px,1fr))",gap:"0.45rem",marginBottom:"2rem"}}>
            {TIME_SLOTS.map(t=>{
              const taken=takenTimes.includes(t),act=selTime===t;
              return(<button key={t} onClick={()=>!taken&&setSelTime(t)} style={{padding:"0.55rem",borderRadius:"9px",border:`1px solid ${taken?"#ff4d6d33":act?"#c9a96e":"#1e1c26"}`,background:taken?"rgba(255,77,109,0.06)":act?"rgba(201,169,110,0.15)":"#13111a",color:taken?"#ff4d6d44":act?"#c9a96e":"#5a5060",cursor:taken?"not-allowed":"pointer",fontSize:"0.8rem",fontWeight:act?600:400,fontFamily:"'DM Sans',sans-serif",textDecoration:taken?"line-through":"none"}}>{t}</button>);
            })}
          </div>
        </>
      ):(
        <div style={{padding:"2.5rem",textAlign:"center",color:"#2a2633",fontSize:"0.8rem",border:"1px dashed #1e1c26",borderRadius:"12px",marginBottom:"1.5rem"}}>← Pick a date to see live availability</div>
      )}
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} disabled={!selDate||!selTime} style={{flex:1}}>Continue →</PrimaryBtn></div>
    </div>
  );
}

function TherapistPicker({therapists,bookings,selDate,selTime,selected,setSelected,onBack,onNext}){
  const isAvail=(t)=>{
    if(!selDate||!selTime) return true;
    const d=selDate.toISOString().slice(0,10);
    return !bookings.some(b=>b.therapist===t.name&&b.date===d&&b.time===selTime&&b.status!=="cancelled");
  };
  const active=therapists.filter(t=>t.active);
  return(
    <div style={{maxWidth:"680px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Your Therapist</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Live availability for your selected date &amp; time. Unavailable = already booked.</p>
      <div style={{display:"flex",flexDirection:"column",gap:"0.85rem",marginBottom:"1.8rem"}}>
        {active.map(t=>{
          const avail=isAvail(t),isSel=selected?.id===t.id;
          return(
            <div key={t.id} onClick={()=>avail&&setSelected(t)} style={{display:"flex",gap:"1rem",alignItems:"center",padding:"1.1rem",background:isSel?`${t.color}10`:"#13111a",border:`1px solid ${isSel?t.color+"55":"#1e1c26"}`,borderRadius:"13px",cursor:avail?"pointer":"not-allowed",opacity:avail?1:0.45,transition:"all 0.2s"}}>
              <div style={{width:"48px",height:"48px",borderRadius:"50%",background:`${t.color}15`,border:`2px solid ${t.color}44`,display:"flex",alignItems:"center",justifyContent:"center",fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:t.color,fontWeight:700,flexShrink:0}}>{t.initials}</div>
              <div style={{flex:1,minWidth:0}}>
                <div style={{display:"flex",alignItems:"center",gap:"0.6rem",flexWrap:"wrap",marginBottom:"0.12rem"}}>
                  <span style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.9rem"}}>{t.name}</span>
                  <span style={{padding:"0.14rem 0.52rem",borderRadius:"20px",fontSize:"0.62rem",fontWeight:600,background:avail?"rgba(92,219,149,0.1)":"rgba(255,77,109,0.1)",color:avail?"#5cdb95":"#ff4d6d"}}>{avail?"✓ Available":"✗ Booked"}</span>
                </div>
                <div style={{fontSize:"0.7rem",color:t.color,marginBottom:"0.18rem"}}>{t.role}</div>
                <div style={{fontSize:"0.7rem",color:"#4a4560"}}>{t.bio}</div>
                <div style={{display:"flex",gap:"0.35rem",marginTop:"0.3rem",flexWrap:"wrap"}}>
                  {t.specialties.split(",").map(s=><span key={s} style={{padding:"0.1rem 0.45rem",borderRadius:"20px",background:"rgba(255,255,255,0.04)",color:"#4a4560",fontSize:"0.6rem"}}>{s.trim()}</span>)}
                </div>
              </div>
              {isSel&&<span style={{color:t.color,fontSize:"1.2rem",flexShrink:0}}>✦</span>}
            </div>
          );
        })}
      </div>
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} disabled={!selected} style={{flex:1}}>Continue →</PrimaryBtn></div>
    </div>
  );
}

function ReviewStep({cart,total,companions,selDate,selTime,therapist,isGift,setIsGift,giftRecipient,setGiftRecipient,giftMessage,setGiftMessage,onBack,onNext}){
  return(
    <div style={{maxWidth:"560px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Review Your Booking</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Confirm everything looks right before paying.</p>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem",marginBottom:"0.9rem"}}>
        <div style={{fontSize:"0.63rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.9rem"}}>Selected Services</div>
        {cart.map(s=>{
          const cp=companions[s.name];
          return(<div key={s.name}>
            <div style={{display:"flex",justifyContent:"space-between",fontSize:"0.83rem",padding:"0.3rem 0",borderBottom:cp?"1px solid #ff9eb522":"1px solid #16141f"}}>
              <span style={{color:"#a89f8c",flex:1,minWidth:0,overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap",paddingRight:"0.5rem"}}>{s.name}</span>
              <span style={{color:"#c9a96e",fontWeight:600,flexShrink:0}}>K{s.price.toLocaleString()}</span>
            </div>
            {cp&&<div style={{display:"flex",justifyContent:"space-between",fontSize:"0.78rem",padding:"0.2rem 0 0.3rem 1rem",borderBottom:"1px solid #16141f"}}>
              <span style={{color:"#ff9eb5"}}>👤 Companion — {cp.name||"Unnamed"}</span>
              <span style={{color:"#ff9eb5",fontWeight:600}}>+K{cp.price.toLocaleString()}</span>
            </div>}
          </div>);
        })}
        <div style={{display:"flex",justifyContent:"space-between",marginTop:"0.7rem",paddingTop:"0.6rem",borderTop:"1px solid #1e1c26"}}>
          <span style={{color:"#5a5060",fontSize:"0.8rem"}}>Total</span>
          <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.4rem",color:"#c9a96e",fontWeight:600}}>K{total.toLocaleString()}</span>
        </div>
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem",marginBottom:"0.9rem"}}>
        <div style={{fontSize:"0.63rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.8rem"}}>Appointment</div>
        {[{i:"📅",v:selDate?fmtDate(selDate):"—"},{i:"🕐",v:selTime||"—"},{i:"👤",v:therapist?.name||"—"},{i:"💼",v:therapist?.role||"—"}].map((r,idx)=>(
          <div key={idx} style={{display:"flex",gap:"0.6rem",fontSize:"0.82rem",padding:"0.3rem 0",borderBottom:"1px solid #16141f"}}><span style={{flexShrink:0}}>{r.i}</span><span style={{color:"#8a7f70"}}>{r.v}</span></div>
        ))}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",marginBottom:"0.9rem"}}>
        <div style={{display:"flex",alignItems:"center",gap:"0.7rem",marginBottom:isGift?"0.8rem":"0"}}>
          <div onClick={()=>setIsGift(!isGift)} style={{width:"20px",height:"20px",borderRadius:"5px",border:`2px solid ${isGift?"#c9a96e":"#2a2633"}`,background:isGift?"#c9a96e":"transparent",display:"flex",alignItems:"center",justifyContent:"center",cursor:"pointer",flexShrink:0,color:isGift?"#0d0b10":"transparent",fontSize:"0.7rem",fontWeight:700}}>{isGift?"✓":""}</div>
          <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>🎁 This is a gift</div><div style={{fontSize:"0.68rem",color:"#5a5060"}}>Give a gift card for someone else to redeem</div></div>
        </div>
        {isGift&&<div style={{display:"flex",flexDirection:"column",gap:"0.4rem",paddingTop:"0.7rem",borderTop:"1px solid #1e1c26"}}>
          <input value={giftRecipient} onChange={e=>setGiftRecipient(e.target.value)} placeholder="Recipient's name" style={{padding:"0.45rem 0.65rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",fontSize:"0.78rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
          <textarea value={giftMessage} onChange={e=>setGiftMessage(e.target.value)} placeholder="Optional gift message…" rows={2} style={{padding:"0.45rem 0.65rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",fontSize:"0.78rem",outline:"none",fontFamily:"'DM Sans',sans-serif",resize:"none"}}/>
        </div>}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",marginBottom:"1.4rem",display:"flex",alignItems:"center",gap:"0.9rem"}}>
        <div style={{width:"36px",height:"36px",borderRadius:"9px",background:"rgba(192,57,43,0.1)",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"1.1rem",flexShrink:0}}>📱</div>
        <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>Airtel Money</div><div style={{fontSize:"0.7rem",color:"#5a5060"}}>Mobile money transfer</div></div>
        <span style={{marginLeft:"auto",color:"#5cdb95",fontSize:"0.7rem",fontWeight:700}}>✓ Selected</span>
      </div>
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} style={{flex:1}}>Confirm Payment →</PrimaryBtn></div>
    </div>
  );
}

function AuthStep({total,onSubmit,onBack}){
  const [mode,setMode]=useState("signup");
  const [form,setForm]=useState({name:"",email:"",phone:"",password:""});
  const [errors,setErrors]=useState({});
  const [loading,setLoading]=useState(false);
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));

  const validate=()=>{
    const e={};
    if(mode==="signup"&&!form.name.trim()) e.name="Required";
    if(!form.email||!form.email.includes("@")) e.email="Valid email required";
    if(!form.phone||form.phone.length<9) e.phone="Valid phone required";
    if(!form.password||form.password.length<6) e.password="Min 6 characters";
    setErrors(e); return !Object.keys(e).length;
  };

  const handle=()=>{ if(!validate()) return; setLoading(true); setTimeout(()=>{setLoading(false);onSubmit(form);},1800); };

  return(
    <div style={{maxWidth:"380px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"18px",padding:"1.8rem"}}>
        <div style={{textAlign:"center",marginBottom:"1.4rem"}}>
          <div style={{fontSize:"2rem",marginBottom:"0.5rem"}}>🔐</div>
          <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.45rem",color:"#e8d5b7",margin:"0 0 0.25rem",fontWeight:600}}>{mode==="signup"?"Create Account":"Welcome Back"}</h2>
          <p style={{color:"#4a4560",fontSize:"0.76rem",margin:0}}>{mode==="signup"?"Sign up to secure your booking":"Log in to complete your booking"}</p>
        </div>
        <div style={{display:"flex",background:"#0d0c13",borderRadius:"10px",padding:"3px",marginBottom:"1.2rem"}}>
          {["signup","login"].map(m=><button key={m} onClick={()=>{setMode(m);setErrors({});}} style={{flex:1,padding:"0.48rem",borderRadius:"8px",border:"none",background:mode===m?"#c9a96e":"transparent",color:mode===m?"#0d0b10":"#4a4560",cursor:"pointer",fontSize:"0.78rem",fontWeight:mode===m?600:400,fontFamily:"'DM Sans',sans-serif",transition:"all 0.2s"}}>{m==="signup"?"Sign Up":"Log In"}</button>)}
        </div>
        <div style={{display:"flex",flexDirection:"column",gap:"0.2rem",marginBottom:"1rem"}}>
          {mode==="signup"&&<FField label="Full Name" error={errors.name}><input style={{...SI,borderColor:errors.name?"#ff4d6d55":"#2a2633"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="Thandiwe Mwanza"/></FField>}
          <FField label="Email" error={errors.email}><input style={{...SI,borderColor:errors.email?"#ff4d6d55":"#2a2633"}} type="email" value={form.email} onChange={e=>upd("email",e.target.value)} placeholder="you@example.com"/></FField>
          <FField label="Phone (Airtel)" error={errors.phone}><input style={{...SI,borderColor:errors.phone?"#ff4d6d55":"#2a2633"}} type="tel" value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="097X XXX XXX"/></FField>
          <FField label="Password" error={errors.password}><input style={{...SI,borderColor:errors.password?"#ff4d6d55":"#2a2633"}} type="password" value={form.password} onChange={e=>upd("password",e.target.value)} placeholder="Min 6 characters"/></FField>
        </div>
        <div style={{padding:"0.7rem 1rem",background:"rgba(201,169,110,0.06)",border:"1px solid rgba(201,169,110,0.15)",borderRadius:"10px",display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"1.1rem"}}>
          <span style={{color:"#5a5060",fontSize:"0.78rem"}}>Amount via Airtel Money</span>
          <span style={{fontFamily:"'Cormorant Garamond',serif",color:"#c9a96e",fontWeight:600,fontSize:"1.1rem"}}>K{total.toLocaleString()}</span>
        </div>
        <button onClick={handle} disabled={loading} style={{width:"100%",padding:"0.85rem",borderRadius:"10px",border:"none",background:"linear-gradient(135deg,#c9a96e,#dfbd7c)",color:"#0d0b10",fontWeight:600,fontSize:"0.88rem",fontFamily:"'DM Sans',sans-serif",cursor:loading?"not-allowed":"pointer",opacity:loading?0.7:1}}>
          {loading?<span style={{display:"flex",alignItems:"center",justifyContent:"center",gap:"0.5rem"}}><span style={{width:"13px",height:"13px",border:"2px solid rgba(13,11,16,0.3)",borderTopColor:"#0d0b10",borderRadius:"50%",animation:"spin 0.8s linear infinite",display:"inline-block"}}/>Processing…</span>:"Confirm & Pay with Airtel Money 📱"}
        </button>
        <GhostBtn onClick={onBack} style={{width:"100%",marginTop:"0.6rem"}}>← Back</GhostBtn>
      </div>
    </div>
  );
}

function ConfirmationStep({bookingRef,total,therapist,selDate,selTime,cart,companions,isGift,giftRecipient,giftCardCode,onSwitchAdmin}){
  const [copied,setCopied]=useState(false);
  const copy=()=>{ try{navigator.clipboard.writeText(bookingRef);}catch(_){} setCopied(true); setTimeout(()=>setCopied(false),2000); };
  return(
    <div style={{maxWidth:"500px",margin:"0 auto",padding:"2rem 1.5rem",animation:"fadeIn 0.5s ease both"}}>
      <div style={{textAlign:"center",marginBottom:"1.8rem"}}>
        <div style={{width:"66px",height:"66px",borderRadius:"50%",background:isGift?"rgba(255,158,181,0.12)":"rgba(92,219,149,0.12)",border:`2px solid ${isGift?"#ff9eb566":"#5cdb9566"}`,display:"flex",alignItems:"center",justifyContent:"center",fontSize:"1.8rem",margin:"0 auto 1rem",boxShadow:"0 0 30px rgba(92,219,149,0.1)"}}>{isGift?"🎁":"✓"}</div>
        <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.8rem",color:isGift?"#ff9eb5":"#5cdb95",margin:"0 0 0.3rem"}}>{isGift?"Gift Card Created!":"Booking Confirmed!"}</h2>
        <p style={{color:"#4a4560",fontSize:"0.8rem",margin:0}}>{isGift?`Your gift card for ${giftRecipient||"someone special"} has been issued. Share the code below.`:"Your slot is reserved. Admin has been notified. Complete Airtel Money payment to finalise."}</p>
      </div>
      {isGift&&<div style={{background:"rgba(255,158,181,0.04)",border:"1px solid rgba(255,158,181,0.15)",borderRadius:"14px",padding:"1.2rem",marginBottom:"0.85rem",textAlign:"center"}}>
        <div style={{fontSize:"0.62rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.4rem"}}>Gift Card Code</div>
        <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.6rem",color:"#ff9eb5",fontWeight:600,letterSpacing:"0.12em",marginBottom:"0.3rem"}}>{giftCardCode}</div>
        <div style={{fontSize:"0.68rem",color:"#5a5060"}}>For: <span style={{color:"#e8d5b7"}}>{giftRecipient}</span> — Value: <span style={{color:"#c9a96e"}}>K{total.toLocaleString()}</span></div>
      </div>}
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem 1.2rem",marginBottom:"0.85rem",display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <div><div style={{fontSize:"0.62rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em"}}>Booking Reference</div><div style={{fontFamily:"'Cormorant Garamond',serif",color:"#c9a96e",fontSize:"1.2rem",fontWeight:600}}>{bookingRef}</div></div>
        <button onClick={copy} style={{padding:"0.3rem 0.8rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"transparent",color:"#8a7f70",cursor:"pointer",fontSize:"0.72rem",fontFamily:"'DM Sans',sans-serif",transition:"all 0.15s"}}>{copied?"✓ Copied":"Copy"}</button>
      </div>
      <div style={{background:"rgba(192,57,43,0.04)",border:"1px solid rgba(192,57,43,0.15)",borderRadius:"14px",padding:"1.2rem",marginBottom:"0.85rem"}}>
        <div style={{display:"flex",alignItems:"center",gap:"0.7rem",marginBottom:"1rem"}}><span style={{fontSize:"1.3rem"}}>📱</span><div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>Pay via Airtel Money</div><div style={{fontSize:"0.68rem",color:"#5a5060"}}>Follow these steps on your phone</div></div></div>
        {[`Dial *778# on your Airtel phone`,`Select "Send Money"`,`Enter number: ${AIRTEL_NUMBER}`,`Enter amount: K${total.toLocaleString()}`,`Reference: ${bookingRef}`,`Confirm with your PIN`].map((s,i)=>(
          <div key={i} style={{display:"flex",gap:"0.6rem",alignItems:"flex-start",marginBottom:"0.45rem"}}>
            <div style={{width:"18px",height:"18px",borderRadius:"50%",background:"rgba(192,57,43,0.15)",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.58rem",fontWeight:700,color:"#c0392b",flexShrink:0,marginTop:"0.05rem"}}>{i+1}</div>
            <span style={{fontSize:"0.78rem",color:"#a89f8c",lineHeight:1.5}}>{s}</span>
          </div>
        ))}
        <div style={{background:"rgba(201,169,110,0.06)",border:"1px solid rgba(201,169,110,0.15)",borderRadius:"10px",padding:"0.7rem",textAlign:"center",marginTop:"0.8rem"}}>
          <div style={{fontSize:"0.6rem",color:"#5a5060",textTransform:"uppercase",letterSpacing:"0.12em",marginBottom:"0.15rem"}}>Send payment to</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.4rem",color:"#c9a96e",fontWeight:600,letterSpacing:"0.06em"}}>{AIRTEL_NUMBER}</div>
        </div>
      </div>
      <div style={{background:"rgba(92,219,149,0.03)",border:"1px solid rgba(92,219,149,0.15)",borderRadius:"14px",padding:"1rem 1.2rem",marginBottom:"1.2rem"}}>
        <div style={{display:"flex",gap:"0.7rem",alignItems:"flex-start"}}>
          <span style={{fontSize:"1rem"}}>🔔</span>
          <div>
            <div style={{fontWeight:500,color:"#5cdb95",fontSize:"0.83rem",marginBottom:"0.2rem"}}>Admin Notified in Real Time</div>
            <div style={{fontSize:"0.72rem",color:"#3a3650",lineHeight:1.5}}>Your booking just appeared live in the admin dashboard. The team can manage it there.</div>
          </div>
        </div>
      </div>
      {onSwitchAdmin&&<button onClick={onSwitchAdmin} style={{width:"100%",padding:"0.75rem",borderRadius:"10px",border:"1px solid rgba(201,169,110,0.2)",background:"rgba(201,169,110,0.05)",color:"#c9a96e",cursor:"pointer",fontSize:"0.8rem",fontWeight:500,fontFamily:"'DM Sans',sans-serif",transition:"all 0.15s"}}>⚙️ View in Admin Panel →</button>}

    </div>
  );
}

function ClientApp({bookings,therapists,services,onNewBooking,onSwitchAdmin,heroImageUrl,onCreateGiftCard,giftCards,onRedeemGiftCard}){
  const [step,setStep]=useState(0);
  const [cart,setCart]=useState([]);
  const [selDate,setSelDate]=useState(null);
  const [selTime,setSelTime]=useState(null);
  const [therapist,setTherapist]=useState(null);
  const [bookingRef,setBookingRef]=useState("");
  const [gcCode,setGcCode]=useState("");
  const [companions,setCompanions]=useState({});
  const [isGift,setIsGift]=useState(false);
  const [giftRecipient,setGiftRecipient]=useState("");
  const [giftMessage,setGiftMessage]=useState("");
  const [showRedeem,setShowRedeem]=useState(false);
  const dates=useMemo(()=>getDates(),[]);
  const total=cart.reduce((s,i)=>s+i.price+(companions[i.name]?.price||0),0);

  const toggleSvc=(svc)=>setCart(c=>c.some(i=>i.name===svc.name)?c.filter(i=>i.name!==svc.name):[...c,svc]);

  const setCompanion=(sn,data)=>{setCompanions(c=>({...c,[sn]:data}));};
  const removeCompanion=(sn)=>{setCompanions(c=>{const n={...c};delete n[sn];return n;});};

  const handleAuth=(userData)=>{
    const ref=genRef();
    const dateStr=selDate?selDate.toISOString().slice(0,10):"";
    const mainSvc=cart[0];
    const compList=cart.filter(i=>companions[i.name]).map(i=>({name:companions[i.name].name,forService:i.name,price:companions[i.name].price}));
    const cNames=compList.map(c=>c.name).filter(Boolean);
    let code="";
    if(isGift&&giftRecipient.trim()){
      code="NGC-"+Math.random().toString(36).slice(2,8).toUpperCase();
      const gc={id:genId(),code,buyer:userData.name||userData.email.split("@")[0],recipient:giftRecipient.trim(),message:giftMessage,services:cart,s:total,status:"active",created:dateStr,branch:therapist?.branch||"woodlands",bookingRef:ref};
      if(onCreateGiftCard)onCreateGiftCard(gc);
    }
    const newBooking={
      id:genId(), ref, source:"client",
      client:userData.name||userData.email.split("@")[0],
      phone:userData.phone, email:userData.email,
      service:mainSvc?.name||"", cat:mainSvc?.cat||"",
      therapist:therapist?.name||"", date:dateStr, time:selTime||"",
      amount:total, status:"pending", payment:"unpaid", payMethod:"",
      note:`${cart.length>1?`+${cart.length-1} more service(s). `:""}${cNames.length?`Companion(s): ${cNames.join(", ")}. `:""}${code?`🎁 Gift for ${giftRecipient.trim()} (code: ${code}). `:""}Booked via app.`,
      companions:compList, isGift:!!code, giftRecipient:code?giftRecipient.trim():"", giftCardCode:code,
    };
    setBookingRef(ref);
    setGcCode(code);
    onNewBooking(newBooking);
    setStep(6);
  };

  const handleRedeem=(gc)=>{
    if(onRedeemGiftCard)onRedeemGiftCard(gc.id);
  };

  return(
    <div style={{minHeight:"100vh",background:"#08070f",color:"#e8d5b7",fontFamily:"'DM Sans',sans-serif",paddingTop: step>0&&step<6?"100px":"50px"}}>
      {showRedeem&&<GiftRedeemModal giftCards={giftCards||[]} onRedeem={handleRedeem} onClose={()=>setShowRedeem(false)}/>}
      {step>0&&step<6&&<div style={{position:"fixed",top:"50px",left:0,right:0,zIndex:50,background:"rgba(8,7,15,0.92)",backdropFilter:"blur(16px)"}}><ProgressBar step={step}/></div>}
      {step===0&&<HeroSection onBook={()=>setStep(1)} heroImageUrl={heroImageUrl} onRedeemGift={()=>setShowRedeem(true)}/>}
      {step===1&&<ServiceSelector cart={cart} toggle={toggleSvc} total={total} services={services} companions={companions} setCompanion={setCompanion} removeCompanion={removeCompanion} onNext={()=>setStep(2)} onBack={()=>setStep(0)}/>}
      {step===2&&<DateTimePicker dates={dates} selDate={selDate} setSelDate={setSelDate} selTime={selTime} setSelTime={setSelTime} bookings={bookings} therapistName={therapist?.name} onBack={()=>setStep(1)} onNext={()=>setStep(3)}/>}
      {step===3&&<TherapistPicker therapists={therapists} bookings={bookings} selDate={selDate} selTime={selTime} selected={therapist} setSelected={setTherapist} onBack={()=>setStep(2)} onNext={()=>setStep(4)}/>}
      {step===4&&<ReviewStep cart={cart} total={total} companions={companions} selDate={selDate} selTime={selTime} therapist={therapist} isGift={isGift} setIsGift={setIsGift} giftRecipient={giftRecipient} setGiftRecipient={setGiftRecipient} giftMessage={giftMessage} setGiftMessage={setGiftMessage} onBack={()=>setStep(3)} onNext={()=>setStep(5)}/>}
      {step===5&&<AuthStep total={total} onSubmit={handleAuth} onBack={()=>setStep(4)}/>}
      {step===6&&<ConfirmationStep bookingRef={bookingRef} total={total} therapist={therapist} selDate={selDate} selTime={selTime} cart={cart} companions={companions} isGift={isGift} giftRecipient={giftRecipient} giftCardCode={gcCode} onSwitchAdmin={onSwitchAdmin}/>}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  ADMIN PANEL
// ═══════════════════════════════════════════════════════════════════════════

const ADMIN_NAV=[{id:"dashboard",icon:"◈",label:"Dashboard"},{id:"bookings",icon:"📋",label:"Bookings"},{id:"extras",icon:"➕",label:"Extras"},{id:"invoices",icon:"🧾",label:"Invoices"},{id:"services",icon:"📦",label:"Services"},{id:"inventory",icon:"📦",label:"Inventory"},{id:"giftcards",icon:"🎁",label:"Gift Cards"},{id:"reports",icon:"📈",label:"Reports"},{id:"appearance",icon:"🎨",label:"Appearance"},{id:"therapists",icon:"👥",label:"Therapists"},{id:"clients",icon:"🧑‍🤝‍🧑",label:"Clients"},{id:"revenue",icon:"📊",label:"Revenue"}];

function Sidebar({view,setView,collapsed,setCollapsed,pending,isSuper}){
  return(
    <div style={{width:collapsed?"54px":"206px",background:"#0b0a10",borderRight:"1px solid #16141f",display:"flex",flexDirection:"column",transition:"width 0.22s ease",flexShrink:0,zIndex:10}}>
      <div style={{padding:collapsed?"1rem 0":"1.1rem",borderBottom:"1px solid #16141f",display:"flex",alignItems:"center",gap:"0.6rem",overflow:"hidden"}}>
        <span style={{fontSize:"1.2rem",flexShrink:0,display:"block",textAlign:"center",width:collapsed?"100%":"auto"}}>🌸</span>
        {!collapsed&&<div><div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",fontWeight:600,whiteSpace:"nowrap",letterSpacing:"0.02em"}}>Ngalula Spa</div><div style={{fontSize:"0.54rem",color:"#3a3650",letterSpacing:"0.14em",textTransform:"uppercase"}}>Admin Panel</div></div>}
      </div>
      <nav style={{flex:1,padding:"0.5rem 0"}}>
        {(isSuper ? ADMIN_NAV : ADMIN_NAV.filter(i=>i.id!=="appearance"&&i.id!=="reports")).map(item=>{
          const act=view===item.id;
          return(
            <div key={item.id} onClick={()=>setView(item.id)} style={{display:"flex",alignItems:"center",gap:"0.65rem",padding:collapsed?"0.65rem 0":"0.55rem 0.9rem",cursor:"pointer",background:act?"rgba(201,169,110,0.08)":"transparent",borderRight:`2px solid ${act?"#c9a96e":"transparent"}`,justifyContent:collapsed?"center":"flex-start",position:"relative",transition:"all 0.15s"}}
              onMouseEnter={e=>{if(!act)e.currentTarget.style.background="rgba(255,255,255,0.02)"}}
              onMouseLeave={e=>{if(!act)e.currentTarget.style.background="transparent"}}
            >
              <span style={{fontSize:"0.82rem",flexShrink:0}}>{item.icon}</span>
              {!collapsed&&<span style={{fontSize:"0.78rem",color:act?"#c9a96e":"#4a4560",fontWeight:act?600:400,whiteSpace:"nowrap"}}>{item.label}</span>}
              {item.id==="bookings"&&pending>0&&!collapsed&&<span style={{marginLeft:"auto",background:"#ef4444",color:"#fff",borderRadius:"10px",fontSize:"0.56rem",fontWeight:700,padding:"0.08rem 0.38rem"}}>{pending}</span>}
              {item.id==="bookings"&&pending>0&&collapsed&&<span style={{position:"absolute",top:"5px",right:"7px",width:"7px",height:"7px",borderRadius:"50%",background:"#ef4444"}}/>}
            </div>
          );
        })}
      </nav>
      <div onClick={()=>setCollapsed(!collapsed)} style={{padding:"0.8rem",borderTop:"1px solid #16141f",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:collapsed?"center":"flex-end",color:"#2a2633"}}>
        <span style={{transform:collapsed?"scaleX(1)":"scaleX(-1)",display:"inline-block",fontSize:"1rem",transition:"transform 0.22s"}}>›</span>
      </div>
    </div>
  );
}

function AdminTopBar({view,unread,onBell,newBookingsCount,currentAdmin,onLogout}){
  const T={dashboard:"Dashboard",bookings:"Bookings",extras:"Extras",invoices:"Invoices",services:"Services",inventory:"Inventory",giftcards:"Gift Cards",reports:"Reports",appearance:"Appearance",therapists:"Therapists",clients:"Clients",revenue:"Revenue & Analytics"};
  return(
    <div style={{height:"50px",background:"#0b0a10",borderBottom:"1px solid #16141f",display:"flex",alignItems:"center",justifyContent:"space-between",padding:"0 1.3rem",flexShrink:0}}>
      <div style={{display:"flex",alignItems:"center",gap:"0.8rem"}}>
        <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7",fontWeight:500}}>{T[view]||view}</span>
        {newBookingsCount>0&&<span style={{padding:"0.15rem 0.6rem",borderRadius:"20px",background:"rgba(92,219,149,0.1)",color:"#5cdb95",fontSize:"0.65rem",fontWeight:600,animation:"pulse 2s ease infinite"}}>🔴 {newBookingsCount} new live booking{newBookingsCount>1?"s":""}</span>}
      </div>
      <div style={{display:"flex",alignItems:"center",gap:"1rem"}}>
        <div onClick={onBell} style={{position:"relative",cursor:"pointer",padding:"4px",borderRadius:"8px",transition:"background 0.2s"}}
          onMouseEnter={e=>e.currentTarget.style.background="rgba(255,255,255,0.04)"}
          onMouseLeave={e=>e.currentTarget.style.background="transparent"}
        >
          <span style={{fontSize:"1rem"}}>🔔</span>
          {unread>0&&<span style={{position:"absolute",top:"1px",right:"1px",background:"#ef4444",color:"#fff",borderRadius:"50%",width:"14px",height:"14px",fontSize:"0.48rem",fontWeight:700,display:"flex",alignItems:"center",justifyContent:"center",boxShadow:"0 0 6px rgba(239,68,68,0.4)"}}>{unread}</span>}
        </div>
        <div style={{display:"flex",alignItems:"center",gap:"0.45rem",position:"relative"}}
          onMouseEnter={e=>e.currentTarget.querySelector('[data-logout]').style.opacity=1}
          onMouseLeave={e=>e.currentTarget.querySelector('[data-logout]').style.opacity=0}
        >
          <div style={{width:"28px",height:"28px",borderRadius:"50%",background:currentAdmin?.role==="superadmin"?"rgba(201,169,110,0.15)":"rgba(92,219,149,0.1)",border:"1px solid",borderColor:currentAdmin?.role==="superadmin"?"#c9a96e44":"#5cdb9544",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.6rem",color:currentAdmin?.role==="superadmin"?"#c9a96e":"#5cdb95",fontWeight:700}}>{currentAdmin?.id==="superadmin"?"★":currentAdmin?.branch==="woodlands"?"🌳":"🌴"}</div>
          <div><div style={{fontSize:"0.72rem",color:"#a89f8c",lineHeight:1.2}}>{currentAdmin?.name?.split("–")[0]?.trim()||"Admin"}</div>{currentAdmin?.branch!=="*"&&<div style={{fontSize:"0.55rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em"}}>{BRANCHES[currentAdmin?.branch]||""}</div>}</div>
          <button data-logout onClick={onLogout} style={{position:"absolute",right:0,top:"100%",opacity:0,transition:"opacity 0.15s",background:"#1a1823",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.3rem 0.7rem",color:"#ef4444",cursor:"pointer",fontSize:"0.65rem",whiteSpace:"nowrap",zIndex:50,marginTop:"4px",fontFamily:"'DM Sans',sans-serif"}}>Sign Out</button>
        </div>
      </div>
    </div>
  );
}

// Booking Form
function BookingFormModal({booking,therapists,services,onSave,onClose}){
  const svcNames=(booking?.service||"").split(",").map(s=>s.trim()).filter(Boolean);
  const empty={client:"",phone:"",services:[],cat:"",therapist:therapists[0]?.name||"",date:"",time:"09:00",amount:0,status:"pending",payment:"unpaid",payMethod:"",note:""};
  const [form,setForm]=useState(booking?{client:booking.client,phone:booking.phone,services:svcNames,cat:booking.cat||"",therapist:booking.therapist,date:booking.date,time:booking.time,amount:booking.amount,status:booking.status,payment:booking.payment,payMethod:booking.payMethod||"",note:booking.note||""}:empty);
  const [errors,setErrors]=useState({});
  const [catF,setCatF]=useState("All");
  const svcList=Array.isArray(services)?services:SERVICES_LIST;
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));
  const toggleSvc=(name)=>{const s=svcList.find(x=>x.name===name);if(!s)return;setForm(f=>{const has=f.services.includes(name);const svcs=has?f.services.filter(n=>n!==name):[...f.services,name];const total=svcs.reduce((sum,n)=>{const sv=svcList.find(x=>x.name===n);return sum+(sv?sv.price:0);},0);return{...f,services:svcs,cat:svcs.length?svcs.map(n=>svcList.find(x=>x.name===n)?.cat).filter(Boolean)[0]||"":"",amount:total};});};
  const total=form.services.reduce((sum,n)=>{const s=svcList.find(x=>x.name===n);return sum+(s?s.price:0);},0);
  const cats=["All",...new Set(svcList.map(s=>s.cat))];
  const filtered=catF==="All"?svcList:svcList.filter(s=>s.cat===catF);
  const validate=()=>{const e={};if(!form.client.trim())e.client="Required";if(!form.phone.trim())e.phone="Required";if(!form.date)e.date="Required";if(form.services.length===0)e.services="Select at least one service";setErrors(e);return!Object.keys(e).length;};
  const R2={display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem"};
  return(
    <BaseModal title={booking?`Edit — ${booking.ref}`:"New Booking"} subtitle={booking?"Update booking details":"Create a new booking"} onClose={onClose} wide>
      <div style={R2}>
        <FField label="Client Name" error={errors.client}><input style={{...SI,borderColor:errors.client?"#ef444455":"#1e1c26"}} value={form.client} onChange={e=>upd("client",e.target.value)} placeholder="Full name"/></FField>
        <FField label="Phone" error={errors.phone}><input style={{...SI,borderColor:errors.phone?"#ef444455":"#1e1c26"}} value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="+260 97 XXX"/></FField>
      </div>
      <div style={R2}>
        <FField label="Therapist"><select style={{...SI,cursor:"pointer"}} value={form.therapist} onChange={e=>upd("therapist",e.target.value)}>{therapists.filter(t=>t.active).map(t=><option key={t.id} value={t.name}>{t.name}</option>)}</select></FField>
        <FField label="Date" error={errors.date}><input style={{...SI,borderColor:errors.date?"#ef444455":"#1e1c26"}} type="date" value={form.date} onChange={e=>upd("date",e.target.value)}/></FField>
      </div>
      <div style={R2}>
        <FField label="Time"><select style={{...SI,cursor:"pointer"}} value={form.time} onChange={e=>upd("time",e.target.value)}>{TIME_SLOTS.map(t=><option key={t} value={t}>{t}</option>)}</select></FField>
        <FField label="Total" error={errors.amount}><div style={{...SI,display:"flex",alignItems:"center",fontWeight:700,color:"#c9a96e",fontSize:"1rem"}}>K{total.toLocaleString()}</div></FField>
      </div>
      <FField label="Services" error={errors.services}>
        <div style={{display:"flex",gap:"0.35rem",marginBottom:"0.55rem",flexWrap:"wrap"}}>{cats.map(c=>{const act=catF===c;return(<button key={c} onClick={()=>setCatF(c)} style={{padding:"0.2rem 0.6rem",borderRadius:"12px",border:`1px solid ${act?"#c9a96e":"#2a2633"}`,background:act?"rgba(201,169,110,0.12)":"transparent",color:act?"#c9a96e":"#4a4560",cursor:"pointer",fontSize:"0.65rem",fontFamily:"'DM Sans',sans-serif"}}>{c}</button>);})}</div>
        <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(170px,1fr))",gap:"0.3rem",maxHeight:"200px",overflowY:"auto",padding:"0.2rem 0"}}>
          {filtered.map(s=>{const sel=form.services.includes(s.name);return(<div key={s.name} onClick={()=>toggleSvc(s.name)} style={{display:"flex",justifyContent:"space-between",alignItems:"center",padding:"0.35rem 0.5rem",background:sel?"rgba(201,169,110,0.06)":"transparent",border:`1px solid ${sel?"#c9a96e44":"#1a1823"}`,borderRadius:"7px",cursor:"pointer",transition:"all 0.12s"}}><span style={{fontSize:"0.72rem",color:sel?"#e8d5b7":"#8a7f70",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap",flex:1,minWidth:0,paddingRight:"0.3rem"}}>{s.name}</span><span style={{fontSize:"0.68rem",fontWeight:600,color:sel?"#c9a96e":"#3a3650",flexShrink:0}}>K{s.price}{sel?" ✓":""}</span></div>);})}
        </div>
      </FField>
      <div style={R2}>
        <FField label="Status"><select style={{...SI,cursor:"pointer"}} value={form.status} onChange={e=>upd("status",e.target.value)}>{Object.entries(STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
        <FField label="Payment"><select style={{...SI,cursor:"pointer"}} value={form.payment} onChange={e=>upd("payment",e.target.value)}>{Object.entries(PAY_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
        <FField label="Payment Method"><select style={{...SI,cursor:"pointer"}} value={form.payMethod} onChange={e=>upd("payMethod",e.target.value)}><option value="">— None —</option>{Object.entries(PAY_METHOD_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
      </div>
      <FField label="Admin Notes"><textarea style={{...SI,resize:"vertical",minHeight:"58px",padding:"0.5rem 0.9rem"}} value={form.note} onChange={e=>upd("note",e.target.value)} placeholder="Special instructions…"/></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.4rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave({...form,service:form.services.join(", "),cat:form.cat||form.services.map(n=>svcList.find(x=>x.name===n)?.cat).filter(Boolean)[0]||"",amount:total});}} style={{flex:2}}>{booking?"Save Changes":"Create Booking"}</PrimaryBtn></div>
    </BaseModal>
  );
}

// Therapist Form
function TherapistFormModal({therapist,onSave,onClose}){
  const empty={name:"",role:"",initials:"",color:"#c9a96e",specialties:"",phone:"",email:"",bio:"",active:true};
  const [form,setForm]=useState(therapist?{name:therapist.name,role:therapist.role,initials:therapist.initials,color:therapist.color,specialties:therapist.specialties,phone:therapist.phone||"",email:therapist.email||"",bio:therapist.bio||"",active:therapist.active}:empty);
  const [errors,setErrors]=useState({});
  const upd=(k,v)=>setForm(f=>{const nf={...f,[k]:v};if(k==="name"&&!therapist){const pts=v.trim().split(" ");nf.initials=pts.map(p=>p[0]||"").join("").toUpperCase().slice(0,2);}return nf;});
  const validate=()=>{const e={};if(!form.name.trim())e.name="Required";if(!form.role.trim())e.role="Required";if(!form.initials.trim())e.initials="Required";setErrors(e);return!Object.keys(e).length;};
  const R2={display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem"};
  return(
    <BaseModal title={therapist?`Edit — ${therapist.name}`:"Add New Therapist"} subtitle="Team member details" onClose={onClose}>
      <div style={R2}>
        <FField label="Full Name" error={errors.name}><input style={{...SI,borderColor:errors.name?"#ef444455":"#1e1c26"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="e.g. Aisha Nkonde"/></FField>
        <FField label="Initials" error={errors.initials}><input style={{...SI,borderColor:errors.initials?"#ef444455":"#1e1c26"}} value={form.initials} onChange={e=>upd("initials",e.target.value.toUpperCase().slice(0,2))} placeholder="AN" maxLength={2}/></FField>
      </div>
      <FField label="Role" error={errors.role}><input style={{...SI,borderColor:errors.role?"#ef444455":"#1e1c26"}} value={form.role} onChange={e=>upd("role",e.target.value)} placeholder="e.g. Senior Massage Therapist"/></FField>
      <FField label="Specialties"><input style={SI} value={form.specialties} onChange={e=>upd("specialties",e.target.value)} placeholder="e.g. Massage, Body Treatments"/></FField>
      <div style={R2}>
        <FField label="Phone"><input style={SI} value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="+260 97 XXX"/></FField>
        <FField label="Email"><input style={SI} type="email" value={form.email} onChange={e=>upd("email",e.target.value)} placeholder="name@spa.com"/></FField>
      </div>
      <FField label="Bio"><input style={SI} value={form.bio} onChange={e=>upd("bio",e.target.value)} placeholder="Short bio…"/></FField>
      <FField label="Profile Colour"><div style={{display:"flex",gap:"0.45rem",flexWrap:"wrap",marginTop:"0.1rem"}}>{COLORS_PRESET.map(c=><div key={c} onClick={()=>upd("color",c)} style={{width:"26px",height:"26px",borderRadius:"50%",background:c,cursor:"pointer",border:`3px solid ${form.color===c?"#fff":"transparent"}`,boxShadow:form.color===c?"0 0 0 2px #c9a96e55":"none"}}/>)}</div></FField>
      <FField label="Status"><select style={{...SI,cursor:"pointer"}} value={form.active?"active":"inactive"} onChange={e=>upd("active",e.target.value==="active")}><option value="active">Active</option><option value="inactive">Inactive</option></select></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.5rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave(form);}} style={{flex:2}}>{therapist?"Save Changes":"Add Therapist"}</PrimaryBtn></div>
    </BaseModal>
  );
}

// Dashboard
function AdminDashboard({
  bookings = [],
  notifications = [],
  onMarkRead,
  heroImageUrl
}) {
  const safeBookings = Array.isArray(bookings) ? bookings : [];
  const safeNotifications = Array.isArray(notifications) ? notifications : [];

  const today = new Date().toISOString().slice(0, 10);

  const todayB = safeBookings.filter(b => b?.date === today);

  const todayRev = todayB
    .filter(b => b?.payment === "paid")
    .reduce((s, b) => s + (b?.amount || 0), 0);

  const pending = safeBookings.filter(b => b?.status === "pending").length;
  const liveBookings = safeBookings.filter(b => b?.source === "client");

  return (
    <div style={{ padding: "1.4rem", height: "100%", overflowY: "auto" }}>

      {/* HEADER */}
      <div style={{
        marginBottom: "1.2rem"
      }}>
        <div style={{
          fontFamily: "'Cormorant Garamond', serif",
          fontSize: "1.3rem",
          color: "#e8d5b7"
        }}>
          Dashboard Overview
        </div>
        <div style={{
          fontSize: "0.8rem",
          color: "#5a5060"
        }}>
          Live insights from your spa booking system
        </div>
      </div>

      {/* KPI GRID */}
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(180px,1fr))",gap:"1rem",marginBottom:"1.2rem"}}>
        {[
          {label:"Revenue Today",value:`K${todayRev.toLocaleString()}`,icon:"💰",color:"#c9a96e"},
          {label:"Bookings",value:todayB.length,icon:"📅",color:"#8b9ef7"},
          {label:"Pending",value:pending,icon:"⏳",color:"#ffb347"},
          {label:"Live Now",value:liveBookings.length,icon:"📱",color:"#5cdb95"}
        ].map((k) => (
          <div key={k.label} style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",cursor:"default"}}>
            <div style={{fontSize:"1.2rem",marginBottom:"0.3rem"}}>{k.icon}</div>
            <div style={{fontSize:"1.5rem",fontWeight:700,color:k.color,lineHeight:1.1}}>{k.value}</div>
            <div style={{fontSize:"0.65rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.08em",marginTop:"0.15rem"}}>{k.label}</div>
          </div>
        ))}
      </div>

      {/* CHART + NOTIFICATIONS GRID */}
      <div style={{display:"grid",gridTemplateColumns:"1.4fr 1fr",gap:"1rem"}}>

        {/* BOOKINGS LIST */}
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
          <div style={{marginBottom:"0.8rem",fontWeight:600,fontSize:"0.85rem"}}>Today's Schedule</div>
          {todayB.length === 0 ? (
            <div style={{color:"#4a4560",fontSize:"0.8rem"}}>No bookings today</div>
          ) : (
            todayB.sort((a,b)=>(a.time||"").localeCompare(b.time||"")).map(b => (
              <div key={b.id} style={{display:"flex",justifyContent:"space-between",padding:"0.5rem 0",borderBottom:"1px solid #16141f"}}>
                <div>
                  <div style={{fontSize:"0.8rem",color:"#c8c0b0"}}>{b.client}</div>
                  <div style={{fontSize:"0.65rem",color:"#4a4560"}}>{b.service}</div>
                </div>
                <div style={{fontSize:"0.75rem",color:"#c9a96e",fontWeight:500}}>{b.time}</div>
              </div>
            ))
          )}
        </div>

        {/* NOTIFICATIONS */}
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
          <div style={{display:"flex",justifyContent:"space-between",marginBottom:"0.8rem"}}>
            <div style={{fontWeight:600,fontSize:"0.85rem"}}>Notifications</div>
            {safeNotifications.some(n=>!n.read) && (
              <button onClick={onMarkRead} style={{background:"none",border:"none",color:"#c9a96e",cursor:"pointer",fontSize:"0.68rem",padding:0}}>Mark all read</button>
            )}
          </div>
          {safeNotifications.slice(0, 6).map(n => (
            <div key={n.id} style={{padding:"0.4rem 0",opacity:n.read?0.4:1,borderBottom:"1px solid #16141f"}}>
              <div style={{fontSize:"0.75rem",color:n.read?"#4a4560":"#a89f8c"}}>{n.msg}</div>
              <div style={{fontSize:"0.6rem",color:"#2a2633",marginTop:"0.12rem"}}>{n.time}</div>
            </div>
          ))}
        </div>

      </div>
    </div>
  );
}

// Bookings CRUD
function AdminBookings({bookings,setBookings,therapists,services,currentAdmin}){
  const [search,setSearch]=useState("");
  const [statusF,setStatusF]=useState("all");
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const SI2={background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"};
  const filtered=useMemo(()=>bookings.filter(b=>(statusF==="all"||b.status===statusF)&&(b.client.toLowerCase().includes(search.toLowerCase())||b.ref.toLowerCase().includes(search.toLowerCase())||b.service.toLowerCase().includes(search.toLowerCase()))).sort((a,b)=>a.date.localeCompare(b.date)||a.time.localeCompare(b.time)),[bookings,search,statusF]);
  const save=(fd)=>{const bd=currentAdmin?.role!=="superadmin"?{...fd,branch:currentAdmin.branch}:fd;if(editing)setBookings(bb=>bb.map(b=>b.id===editing.id?{...b,...bd}:b));else setBookings(bb=>[...bb,{...bd,id:genId(),ref:genRef(),source:"admin"}]);setShowForm(false);setEditing(null);};
  const del=(id)=>{setBookings(bb=>bb.filter(b=>b.id!==id));setDeleting(null);};
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto",display:"flex",flexDirection:"column",gap:"0.9rem"}}>
      <div style={{display:"flex",gap:"0.6rem",flexWrap:"wrap",alignItems:"center"}}>
        <input placeholder="Search client, ref, service…" value={search} onChange={e=>setSearch(e.target.value)} style={{...SI2,flex:"1 1 160px"}}/>
        <select value={statusF} onChange={e=>setStatusF(e.target.value)} style={{...SI2,cursor:"pointer"}}><option value="all">All Statuses</option>{Object.entries(STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select>
        <span style={{fontSize:"0.68rem",color:"#2a2633"}}>{filtered.length} shown</span>
        <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}} style={{marginLeft:"auto",whiteSpace:"nowrap",padding:"0.5rem 1.1rem"}}>+ New Booking</PrimaryBtn>
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",overflow:"hidden"}}>
        <div style={{overflowX:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.77rem"}}>
            <thead><tr style={{borderBottom:"1px solid #16141f",background:"rgba(255,255,255,0.02)"}}>{["Src","Ref","Client","Service","Therapist","Date/Time","Amount","Status","Payment","Method","Actions"].map(h=><th key={h} style={{padding:"0.62rem 0.8rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.1em",whiteSpace:"nowrap"}}>{h}</th>)}</tr></thead>
            <tbody>
              {filtered.length===0?<tr><td colSpan={11} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No bookings match</td></tr>:filtered.map((b,i)=>(
                <tr key={b.id} style={{borderBottom:"1px solid #111019",background:b.source==="client"?"rgba(92,219,149,0.02)":"transparent"}}
                  onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.04)"}
                  onMouseLeave={e=>e.currentTarget.style.background=b.source==="client"?"rgba(92,219,149,0.02)":"transparent"}
                >
                  <td style={{padding:"0.55rem 0.8rem"}}><span title={b.source==="client"?"Live from app":"Admin entry"} style={{fontSize:"0.7rem"}}>{b.source==="client"?"🔵":"⚙️"}</span></td>
                  <td style={{padding:"0.55rem 0.8rem",color:"#c9a96e",fontFamily:"monospace",fontSize:"0.66rem"}}>{b.ref}</td>
                  <td style={{padding:"0.55rem 0.8rem"}}><div style={{fontWeight:500,color:"#c8c0b0",whiteSpace:"nowrap",fontSize:"0.78rem"}}>{b.client}</div><div style={{fontSize:"0.6rem",color:"#2a2633"}}>{b.phone}</div></td>
                  <td style={{padding:"0.55rem 0.8rem",color:"#5a5060",maxWidth:"130px",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap",fontSize:"0.73rem"}}>{b.service}</td>
                  <td style={{padding:"0.55rem 0.8rem",color:"#4a4560",fontSize:"0.7rem",whiteSpace:"nowrap"}}>{b.therapist?.split(" ")[0]}</td>
                  <td style={{padding:"0.55rem 0.8rem",whiteSpace:"nowrap"}}><div style={{color:"#6e6460",fontSize:"0.7rem"}}>{b.date}</div><div style={{color:"#c9a96e",fontWeight:600,fontSize:"0.73rem"}}>{b.time}</div></td>
                  <td style={{padding:"0.55rem 0.8rem",color:"#c9a96e",fontWeight:700,whiteSpace:"nowrap",fontSize:"0.78rem"}}>K{b.amount?.toLocaleString()}</td>
                  <td style={{padding:"0.55rem 0.8rem"}}><SBadge s={b.status}/></td>
                  <td style={{padding:"0.55rem 0.8rem"}}><PBadge s={b.payment}/></td>
                  <td style={{padding:"0.55rem 0.8rem"}}><MethodBadge m={b.payMethod}/></td>
                  <td style={{padding:"0.55rem 0.8rem"}}>
                    <div style={{display:"flex",gap:"0.25rem",flexWrap:"wrap"}}>
                      {b.status==="pending"&&<button onClick={()=>setBookings(bb=>bb.map(x=>x.id===b.id?{...x,status:"confirmed"}:x))} style={{padding:"0.14rem 0.38rem",borderRadius:"5px",border:"1px solid rgba(92,219,149,0.25)",background:"transparent",color:"#5cdb95",cursor:"pointer",fontSize:"0.58rem"}}>✓</button>}
                      {b.payment==="unpaid"&&b.status!=="cancelled"&&<select onChange={e=>{const v=e.target.value;if(v)setBookings(bb=>bb.map(x=>x.id===b.id?{...x,payment:"paid",payMethod:v}:x));}} style={{padding:"0.1rem 0.2rem",borderRadius:"5px",border:"1px solid rgba(201,169,110,0.25)",background:"transparent",color:"#c9a96e",cursor:"pointer",fontSize:"0.55rem",maxWidth:"68px",outline:"none"}}><option value="">$ Pay</option>{Object.entries(PAY_METHOD_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select>}
                      {b.payMethod&&<select value="" onChange={e=>{const v=e.target.value;if(v)setBookings(bb=>bb.map(x=>x.id===b.id?{...x,payMethod:v}:x));}} style={{padding:"0.1rem 0.2rem",borderRadius:"5px",border:"1px solid #2a2633",background:"transparent",color:"#5a5060",cursor:"pointer",fontSize:"0.55rem",maxWidth:"68px",outline:"none"}}><option value="">{PAY_METHOD_META[b.payMethod]?.l||b.payMethod}</option>{Object.entries(PAY_METHOD_META).filter(([k])=>k!==b.payMethod).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select>}
                      <button onClick={()=>{setEditing(b);setShowForm(true);}} style={{padding:"0.14rem 0.38rem",borderRadius:"5px",border:"1px solid #1e1c26",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.58rem"}}>✏</button>
                      <button onClick={()=>setDeleting(b)} style={{padding:"0.14rem 0.38rem",borderRadius:"5px",border:"1px solid rgba(239,68,68,0.2)",background:"transparent",color:"#ef4444",cursor:"pointer",fontSize:"0.58rem"}}>🗑</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #16141f",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}>
          <span>{filtered.length} of {bookings.length} · 🔵 = live from app</span>
          <span>Total: K{filtered.reduce((s,b)=>s+(b.amount||0),0).toLocaleString()}</span>
        </div>
      </div>
      {showForm&&<BookingFormModal booking={editing} therapists={therapists} services={services} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="booking" name={`${deleting.ref} — ${deleting.client}`} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

// Therapists CRUD
function AdminTherapists({therapists,setTherapists,bookings,currentAdmin}){
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const save=(fd)=>{const bd=currentAdmin?.role!=="superadmin"?{...fd,branch:currentAdmin.branch}:fd;if(editing)setTherapists(tt=>tt.map(t=>t.id===editing.id?{...t,...bd}:t));else setTherapists(tt=>[...tt,{...bd,id:genId(),sessions:0,rating:5.0,revenue:0,active:true}]);setShowForm(false);setEditing(null);};
  const del=(id)=>{setTherapists(tt=>tt.filter(t=>t.id!==id));setDeleting(null);};
  const today=new Date().toISOString().slice(0,10);
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%"}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"1.2rem"}}>
        <div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>Therapist Management</h3><p style={{margin:"0.15rem 0 0",fontSize:"0.7rem",color:"#3a3650"}}>{therapists.filter(t=>t.active).length} active · {therapists.length} total — changes reflect instantly in client booking</p></div>
        <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}}>+ Add Therapist</PrimaryBtn>
      </div>
      <div style={{background:"rgba(92,219,149,0.03)",border:"1px solid rgba(92,219,149,0.12)",borderRadius:"11px",padding:"0.7rem 1rem",marginBottom:"1.2rem",fontSize:"0.72rem",color:"#5cdb95"}}>
        ⚡ Real-time sync: therapists added or deactivated here are immediately reflected in the client booking flow.
      </div>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(280px,1fr))",gap:"1rem"}}>
        {therapists.map(t=>{
          const tB=bookings.filter(b=>b.therapist===t.name);
          const todayB=tB.filter(b=>b.date===today);
          return(
            <div key={t.id} style={{background:"#0f0d14",border:`1px solid ${t.color}22`,borderRadius:"14px",padding:"1.2rem",position:"relative",overflow:"hidden",opacity:t.active?1:0.5}}>
              <div style={{position:"absolute",top:0,left:0,right:0,height:"3px",background:`linear-gradient(90deg,${t.color},transparent)`}}/>
              <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"0.9rem"}}>
                <div style={{display:"flex",gap:"0.75rem",alignItems:"center"}}>
                  <div style={{width:"44px",height:"44px",borderRadius:"50%",background:`${t.color}15`,border:`2px solid ${t.color}44`,display:"flex",alignItems:"center",justifyContent:"center",fontFamily:"'Cormorant Garamond',serif",fontSize:"0.88rem",color:t.color,fontWeight:700,flexShrink:0}}>{t.initials}</div>
                  <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.86rem"}}>{t.name}</div><div style={{fontSize:"0.67rem",color:t.color,marginTop:"0.06rem"}}>{t.role}</div></div>
                </div>
                <div style={{display:"flex",flexDirection:"column",gap:"0.28rem",alignItems:"flex-end"}}>
                  <button onClick={()=>setTherapists(tt=>tt.map(x=>x.id===t.id?{...x,active:!x.active}:x))} style={{padding:"0.15rem 0.5rem",borderRadius:"20px",border:`1px solid ${t.active?"#5cdb9544":"#ff4d6d44"}`,background:"transparent",color:t.active?"#5cdb95":"#ff4d6d",cursor:"pointer",fontSize:"0.6rem",fontWeight:600}}>{t.active?"Active":"Inactive"}</button>
                  <div style={{display:"flex",gap:"0.28rem"}}>
                    <button onClick={()=>{setEditing(t);setShowForm(true);}} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #2a2633",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.62rem"}}>✏ Edit</button>
                    <button onClick={()=>setDeleting(t)} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #ff4d6d33",background:"transparent",color:"#ff4d6d",cursor:"pointer",fontSize:"0.62rem"}}>🗑</button>
                  </div>
                </div>
              </div>
              <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:"0.4rem",marginBottom:"0.8rem"}}>
                {[{l:"Sessions",v:t.sessions},{l:"Rating",v:`${t.rating}⭐`},{l:"Revenue",v:`K${(t.revenue/1000).toFixed(0)}k`}].map(s=>(
                  <div key={s.l} style={{textAlign:"center",background:"rgba(255,255,255,0.03)",borderRadius:"7px",padding:"0.42rem 0.2rem"}}>
                    <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.88rem",color:t.color,fontWeight:600}}>{s.v}</div>
                    <div style={{fontSize:"0.57rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em"}}>{s.l}</div>
                  </div>
                ))}
              </div>
              {(t.phone||t.email)&&<div style={{background:"rgba(255,255,255,0.02)",borderRadius:"7px",padding:"0.48rem 0.6rem",marginBottom:"0.7rem",fontSize:"0.66rem"}}>{t.phone&&<div style={{color:"#5a5060",marginBottom:"0.12rem"}}>📞 {t.phone}</div>}{t.email&&<div style={{color:"#5a5060"}}>✉ {t.email}</div>}</div>}
              <div style={{fontSize:"0.62rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.45rem"}}>Today ({todayB.length})</div>
              {todayB.length===0?<div style={{fontSize:"0.7rem",color:"#2a2633",fontStyle:"italic"}}>No bookings today</div>:todayB.map(b=>(
                <div key={b.id} style={{display:"flex",gap:"0.5rem",alignItems:"center",padding:"0.32rem 0",borderTop:"1px solid #1a1823"}}>
                  {b.source==="client"&&<span style={{width:"5px",height:"5px",borderRadius:"50%",background:"#5cdb95",flexShrink:0}} title="From app"/>}
                  <span style={{color:t.color,fontSize:"0.7rem",fontWeight:600,minWidth:"36px"}}>{b.time}</span>
                  <div style={{flex:1,minWidth:0}}><div style={{fontSize:"0.7rem",color:"#8a7f70",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.client}</div></div>
                  <SBadge s={b.status}/>
                </div>
              ))}
              <div style={{marginTop:"0.6rem",paddingTop:"0.5rem",borderTop:"1px solid #1a1823",fontSize:"0.64rem",color:"#2a2633"}}>{tB.length} total on record</div>
            </div>
          );
        })}
      </div>
      {showForm&&<TherapistFormModal therapist={editing} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="therapist" name={deleting.name} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

function AdminClients({bookings}){
  const clients=useMemo(()=>{const map={};bookings.forEach(b=>{if(!map[b.client])map[b.client]={name:b.client,phone:b.phone,visits:0,spent:0,last:"",bookings:[]};map[b.client].visits++;if(b.payment==="paid")map[b.client].spent+=b.amount||0;if(!map[b.client].last||b.date>map[b.client].last)map[b.client].last=b.date;map[b.client].bookings.push(b);});return Object.values(map).sort((a,b)=>b.spent-a.spent);},[bookings]);
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto"}}>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",overflow:"hidden"}}>
        <div style={{overflowX:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.77rem"}}>
            <thead><tr style={{borderBottom:"1px solid #16141f",background:"rgba(255,255,255,0.02)"}}>{["#","Client","Phone","Bookings","Paid","Last Visit","History"].map(h=><th key={h} style={{padding:"0.62rem 0.8rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.1em",whiteSpace:"nowrap"}}>{h}</th>)}</tr></thead>
            <tbody>
              {clients.map((c,i)=>{
                const tier=c.spent>8000?{l:"VIP",col:"#c9a96e"}:c.spent>3000?{l:"Regular",col:"#5cdb95"}:{l:"New",col:"#8b9ef7"};
                const hasLive=c.bookings.some(b=>b.source==="client");
                return(
                  <tr key={c.name} style={{borderBottom:"1px solid #111019",background:hasLive?"rgba(92,219,149,0.02)":"transparent"}}
                    onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.04)"}
                    onMouseLeave={e=>e.currentTarget.style.background=hasLive?"rgba(92,219,149,0.02)":"transparent"}
                  >
                    <td style={{padding:"0.55rem 0.8rem",color:"#2a2633",fontSize:"0.66rem"}}>{i+1}</td>
                    <td style={{padding:"0.55rem 0.8rem"}}><div style={{display:"flex",alignItems:"center",gap:"0.55rem"}}><div style={{width:"24px",height:"24px",borderRadius:"50%",background:"rgba(201,169,110,0.08)",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.58rem",color:"#c9a96e",fontWeight:700,flexShrink:0}}>{c.name.split(" ").map(w=>w[0]).join("").slice(0,2)}</div><div><div style={{fontWeight:500,color:"#c8c0b0",whiteSpace:"nowrap",fontSize:"0.78rem"}}>{c.name}{hasLive&&<span style={{marginLeft:"0.35rem",fontSize:"0.55rem",color:"#8b9ef7"}}>◈</span>}</div><span style={{fontSize:"0.55rem",padding:"0.04rem 0.35rem",borderRadius:"10px",background:`${tier.col}12`,color:tier.col,fontWeight:600}}>{tier.l}</span></div></div></td>
                    <td style={{padding:"0.55rem 0.8rem",fontSize:"0.7rem",color:"#4a4560"}}>{c.phone}</td>
                    <td style={{padding:"0.55rem 0.8rem",color:"#8b9ef7",fontWeight:600,textAlign:"center",fontSize:"0.78rem"}}>{c.visits}</td>
                    <td style={{padding:"0.55rem 0.8rem",color:"#c9a96e",fontWeight:700,fontSize:"0.78rem"}}>K{c.spent.toLocaleString()}</td>
                    <td style={{padding:"0.55rem 0.8rem",color:"#4a4560",fontSize:"0.7rem",whiteSpace:"nowrap"}}>{c.last}</td>
                    <td style={{padding:"0.55rem 0.8rem"}}><div style={{display:"flex",gap:"0.15rem",alignItems:"center"}}>{c.bookings.slice(0,6).map(b=><div key={b.id} style={{width:"6px",height:"6px",borderRadius:"2px",background:STATUS_META[b.status]?.c||"#2a2633"}} title={b.status}/>)}{c.bookings.length>6&&<span style={{fontSize:"0.55rem",color:"#2a2633",marginLeft:"0.15rem"}}>+{c.bookings.length-6}</span>}</div></td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #16141f",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}><span>{clients.length} unique clients · ◈ = booked via app</span><span>Lifetime paid: K{clients.reduce((s,c)=>s+c.spent,0).toLocaleString()}</span></div>
      </div>
    </div>
  );
}

function AdminRevenue({bookings}){
  const bkTotal=(b)=>(b.amount||0)+(b.extras||[]).reduce((s,x)=>s+(x.amount||0),0);
  const total=bookings.reduce((s,b)=>s+bkTotal(b),0);
  const CT=({active,payload,label})=>!active||!payload?.length?null:(<div style={{background:"#1a1823",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.55rem 0.8rem",fontSize:"0.74rem"}}><div style={{color:"#5a5060"}}>{label}</div><div style={{color:"#c9a96e",fontWeight:600}}>K{payload[0].value.toLocaleString()}</div></div>);
  const totalRev=bookings.reduce((s,b)=>s+bkTotal(b),0);
  const massageRev=bookings.filter(b=>b.cat==="Massage").reduce((s,b)=>s+bkTotal(b),0);
  const bodyRev=bookings.filter(b=>b.cat==="Body Treatments").reduce((s,b)=>s+bkTotal(b),0);
  const facialRev=bookings.filter(b=>b.cat==="Facials").reduce((s,b)=>s+bkTotal(b),0);
  const nailsRev=bookings.filter(b=>b.cat==="Nails").reduce((s,b)=>s+bkTotal(b),0);
  const lashesRev=bookings.filter(b=>b.cat==="Lashes").reduce((s,b)=>s+bkTotal(b),0);
  const maxCat=Math.max(massageRev,bodyRev,facialRev,nailsRev,lashesRev,1);
  const cats=[{n:"Massage",rev:massageRev,c:"#c9a96e"},{n:"Body Treatments",rev:bodyRev,c:"#5cdb95"},{n:"Facials",rev:facialRev,c:"#a78bfa"},{n:"Nails",rev:nailsRev,c:"#f472b6"},{n:"Lashes",rev:lashesRev,c:"#60a5fa"}];
  const catPieData=cats.filter(c=>c.rev>0).map(c=>({name:c.n,value:c.rev,color:c.c}));
  const paidRev=bookings.filter(b=>b.payment==="paid").reduce((s,b)=>s+bkTotal(b),0);
  const unpaidRev=bookings.filter(b=>b.payment!=="paid"&&b.status!=="cancelled").reduce((s,b)=>s+bkTotal(b),0);
  const payPieData=[{name:"Collected",value:paidRev,color:"#5cdb95"},{name:"Outstanding",value:unpaidRev,color:"#ef4444"}].filter(d=>d.value>0);
  const completedCount=bookings.filter(b=>b.status==="completed").length;
  const cancelledCount=bookings.filter(b=>b.status==="cancelled").length;
  const confirmedCount=bookings.length-completedCount-cancelledCount;
  const statusPieData=[{name:"Completed",value:completedCount,color:"#5cdb95"},{name:"Confirmed",value:confirmedCount,color:"#8b9ef7"},{name:"Cancelled",value:cancelledCount,color:"#ef4444"}].filter(d=>d.value>0);
  const revVals=REVENUE_DATA.map(d=>d.rev);
  const n=revVals.length;
  const indices=revVals.map((_,i)=>i);
  const sumX=indices.reduce((s,x)=>s+x,0);
  const sumY=revVals.reduce((s,y)=>s+y,0);
  const sumXY=indices.reduce((s,x,i)=>s+x*revVals[i],0);
  const sumX2=indices.reduce((s,x)=>s+x*x,0);
  const slope=n*sumXY-sumX*sumY===0?0:(n*sumXY-sumX*sumY)/(n*sumX2-sumX*sumX);
  const intercept=(sumY-slope*sumX)/n;
  const lastDay=n-1;
  const predDays=7;
  const predictions=Array.from({length:predDays},(_,i)=>({day:`+${i+1}`,pred:Math.round(Math.max(0,intercept+slope*(lastDay+1+i))),actual:null}));
  const chartData=REVENUE_DATA.map((d,i)=>({...d,trend:Math.round(Math.max(0,intercept+slope*i))})).concat(predictions.map(p=>({day:p.day,rev:null,trend:null,pred:p.pred})));
  const recentAvg=revVals.slice(-7).reduce((s,v)=>s+v,0)/7;
  const predictedAvg=predictions.reduce((s,p)=>s+p.pred,0)/predDays;
  const trendDir=slope>0?"up":slope<0?"down":"flat";
  const weekOverWeek=REVENUE_DATA.length>=14?`${(((revVals.slice(-7).reduce((s,v)=>s+v,0)/7)/(revVals.slice(-14,-7).reduce((s,v)=>s+v,0)/7)-1)*100).toFixed(1)}%`:null;
  const catNames=cats.map(c=>c.n);
  const revPerCat=cats.map(c=>c.rev);
  const topCat=cats.reduce((a,b)=>a.rev>b.rev?a:b,{n:"None",rev:0});
  const worstCat=cats.reduce((a,b)=>a.rev<b.rev?a:b,{n:"None",rev:0});
  const strategies=[];
  if(worstCat.rev>=0&&worstCat.n!=="None"&&worstCat.rev<topCat.rev*0.3)strategies.push({icon:"📢",title:`Promote ${worstCat.n}`,desc:`${worstCat.n} generates only K${worstCat.rev.toLocaleString()} vs K${topCat.rev.toLocaleString()} for ${topCat.n}. Run a "Try ${worstCat.n}" campaign with a 15% discount to increase awareness.`});
  if(unpaidRev>paidRev*0.2)strategies.push({icon:"💳",title:"Reduce Outstanding Payments",desc:`K${unpaidRev.toLocaleString()} is outstanding — ${Math.round((unpaidRev/(paidRev+unpaidRev||1))*100)}% of revenue. Introduce a 5% prepaid discount or auto-reminder SMS to improve collection.`});
  if(cancelledCount>bookings.length*0.1)strategies.push({icon:"📅",title:"Reduce Cancellations",desc:`${Math.round((cancelledCount/bookings.length)*100)}% of bookings cancelled. Send confirmation reminders 24h before and offer flexible rescheduling to lower this rate.`});
  if(trendDir==="up")strategies.push({icon:"📈",title:"Capitalise on Upward Trend",desc:`Revenue is trending up (${weekOverWeek||"improving"} week-over-week). Consider raising capacity on peak days and launching a referral program.`});
  else if(trendDir==="down")strategies.push({icon:"📉",title:"Reverse the Downtrend",desc:`Revenue is declining (${weekOverWeek||"slowing"} week-over-week). Launch a "Back to Wellness" promotion — bundle 3 sessions for the price of 2.`});
  else strategies.push({icon:"⚖️",title:"Maintain Momentum",desc:`Revenue is stable. A loyalty card (5th visit free) or a "Bring a Friend" discount could boost without major investment.`});
  if(bookings.filter(b=>b.source==="client").length<bookings.length*0.2)strategies.push({icon:"📱",title:"Boost App Bookings",desc:`Only ${Math.round((bookings.filter(b=>b.source==="client").length/bookings.length)*100)}% of bookings come via the app. Push in-spa QR codes and SMS links to drive digital adoption.`});
  if(strategies.length<3)strategies.push({icon:"🌟",title:"Upsell Extra Services",desc:`Only ${bookings.filter(b=>(b.extras||[]).length>0).length} bookings have extras. Train therapists to suggest add-ons (aromatherapy, scalp massage) during sessions.`});
  const PIE_COLS=["#c9a96e","#5cdb95","#a78bfa","#f472b6","#60a5fa","#fbbf24","#ef4444","#8b9ef7"];
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%",display:"flex",flexDirection:"column",gap:"1rem"}}>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(135px,1fr))",gap:"0.9rem"}}>
        {[{i:"💰",l:"14-Day Revenue",v:`K${total.toLocaleString()}`,c:"#c9a96e"},{i:"📅",l:"Total Bookings",v:bookings.length,c:"#8b9ef7"},{i:"✅",l:"Completed",v:completedCount,c:"#5cdb95"},{i:"📱",l:"Via App",v:bookings.filter(b=>b.source==="client").length,c:"#a78bfa"}].map(c=>(
          <div key={c.l} style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"12px",padding:"0.95rem",position:"relative",overflow:"hidden"}}>
            <div style={{position:"absolute",top:0,left:0,right:0,height:"2px",background:`linear-gradient(90deg,${c.c}55,transparent)`}}/>
            <div style={{fontSize:"1rem",marginBottom:"0.25rem"}}>{c.i}</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.35rem",color:c.c,fontWeight:600}}>{c.v}</div>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em"}}>{c.l}</div>
          </div>
        ))}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"baseline",marginBottom:"0.9rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7"}}>Revenue Trend & 7-Day Forecast</div>
          <div style={{fontSize:"0.6rem",color:"#3a3650"}}>
            {trendDir==="up"?<span style={{color:"#5cdb95"}}>▲ Trending up</span>:trendDir==="down"?<span style={{color:"#ef4444"}}>▼ Trending down</span>:<span style={{color:"#c9a96e"}}>— Stable</span>}
            {weekOverWeek&&<span style={{marginLeft:"0.5rem"}}>{weekOverWeek} vs last week</span>}
          </div>
        </div>
        <ResponsiveContainer width="100%" height={190}>
          <LineChart data={chartData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#16141f" vertical={false}/>
            <XAxis dataKey="day" tick={{fill:"#3a3650",fontSize:7}} axisLine={false} tickLine={false} interval="preserveStartEnd"/>
            <YAxis tick={{fill:"#3a3650",fontSize:8}} axisLine={false} tickLine={false} width={40} tickFormatter={v=>`K${(v/1000).toFixed(0)}k`}/>
            <Tooltip contentStyle={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"8px",fontSize:"0.72rem"}}/>
            <Bar dataKey="rev" radius={[3,3,0,0]} name="Actual">{REVENUE_DATA.map((_,i)=><Cell key={i} fill={i===REVENUE_DATA.length-1?"#c9a96e":"#1e1c26"}/>)}</Bar>
            <Line type="monotone" dataKey="trend" stroke="#a78bfa" strokeWidth={1.5} dot={false} strokeDasharray="4 3" name="Trend"/>
            <Line type="monotone" dataKey="pred" stroke="#fbbf24" strokeWidth={2} dot={{fill:"#fbbf24",r:3}} strokeDasharray="6 3" name="Forecast"/>
          </LineChart>
        </ResponsiveContainer>
        <div style={{display:"flex",gap:"1rem",marginTop:"0.6rem",flexWrap:"wrap"}}>
          <div style={{fontSize:"0.64rem",color:"#4a4560"}}>Recent avg: <span style={{color:"#c8c0b0",fontWeight:600}}>K{recentAvg.toLocaleString(undefined,{maximumFractionDigits:0})}</span></div>
          <div style={{fontSize:"0.64rem",color:"#4a4560"}}>Forecast avg: <span style={{color:"#fbbf24",fontWeight:600}}>K{predictedAvg.toLocaleString(undefined,{maximumFractionDigits:0})}</span></div>
          <div style={{fontSize:"0.64rem",color:"#4a4560"}}>Projected 7-day: <span style={{color:"#c9a96e",fontWeight:600}}>K{predictions.reduce((s,p)=>s+p.pred,0).toLocaleString()}</span></div>
        </div>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(240px,1fr))",gap:"1rem"}}>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>By Category</div>
          {cats.map(c=><div key={c.n} style={{marginBottom:"0.8rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#5a5060"}}>{c.n}</span><span style={{color:c.c,fontWeight:600}}>K{c.rev.toLocaleString()}</span></div><div style={{background:"#0d0c13",borderRadius:"4px",height:"6px"}}><div style={{width:`${Math.round((c.rev/maxCat)*100)}%`,height:"100%",background:`linear-gradient(90deg,${c.c}77,${c.c})`,borderRadius:"4px"}}/></div></div>)}
        </div>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Payment Status</div>
          {(()=>{const paid=bookings.filter(b=>b.payment==="paid").reduce((s,b)=>s+bkTotal(b),0);const unpaid=bookings.filter(b=>b.payment==="unpaid"&&b.status!=="cancelled").reduce((s,b)=>s+bkTotal(b),0);return[{l:"Collected",v:paid,c:"#5cdb95"},{l:"Outstanding",v:unpaid,c:"#ef4444"}].map(r=><div key={r.l} style={{marginBottom:"0.85rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#5a5060"}}>{r.l}</span><span style={{color:r.c,fontWeight:600}}>K{r.v.toLocaleString()}</span></div><div style={{background:"#0d0c13",borderRadius:"4px",height:"6px"}}><div style={{width:`${Math.round((r.v/(paid+unpaid||1))*100)}%`,height:"100%",background:`linear-gradient(90deg,${r.c}77,${r.c})`,borderRadius:"4px"}}/></div></div>);})()}
        </div>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(280px,1fr))",gap:"1rem"}}>
        {catPieData.length>0&&(
          <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.5rem"}}>Revenue by Category</div>
            <ResponsiveContainer width="100%" height={180}>
              <PieChart>
                <Pie data={catPieData} cx="50%" cy="50%" outerRadius={70} dataKey="value" label={({name,value})=>`${name} K${(value/1000).toFixed(0)}k`}>
                  {catPieData.map((e,i)=><Cell key={i} fill={e.color||PIE_COLS[i%PIE_COLS.length]}/>)}
                </Pie>
                <Tooltip formatter={v=>`K${v.toLocaleString()}`}/>
              </PieChart>
            </ResponsiveContainer>
          </div>
        )}
        {payPieData.length>0&&(
          <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.5rem"}}>Payment Split</div>
            <ResponsiveContainer width="100%" height={180}>
              <PieChart>
                <Pie data={payPieData} cx="50%" cy="50%" outerRadius={70} dataKey="value" label={({name,value})=>`${name} K${(value/1000).toFixed(0)}k`}>
                  {payPieData.map((e,i)=><Cell key={i} fill={e.color}/>)}
                </Pie>
                <Tooltip formatter={v=>`K${v.toLocaleString()}`}/>
              </PieChart>
            </ResponsiveContainer>
          </div>
        )}
        {statusPieData.length>0&&(
          <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.5rem"}}>Booking Status</div>
            <ResponsiveContainer width="100%" height={180}>
              <PieChart>
                <Pie data={statusPieData} cx="50%" cy="50%" outerRadius={70} dataKey="value" label={({name,value})=>`${name} ${value}`}>
                  {statusPieData.map((e,i)=><Cell key={i} fill={e.color}/>)}
                </Pie>
                <Tooltip/>
              </PieChart>
            </ResponsiveContainer>
          </div>
        )}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"baseline",marginBottom:"0.8rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7"}}>AI-Powered Suggestions</div>
          <span style={{fontSize:"0.58rem",color:"#2a2633"}}>Based on current data</span>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(250px,1fr))",gap:"0.8rem"}}>
          {strategies.map(s=>(
            <div key={s.title} style={{background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"10px",padding:"0.85rem"}}>
              <div style={{fontSize:"1.1rem",marginBottom:"0.3rem"}}>{s.icon}</div>
              <div style={{fontWeight:600,fontSize:"0.78rem",color:"#e8d5b7",marginBottom:"0.25rem"}}>{s.title}</div>
              <div style={{fontSize:"0.68rem",color:"#5a5060",lineHeight:1.4}}>{s.desc}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function AdminExtras({bookings,setBookings}){
  const [search,setSearch]=useState("");
  const [selBk,setSelBk]=useState(null);
  const [addName,setAddName]=useState("");
  const [addAmt,setAddAmt]=useState("");
  const bkList=bookings.filter(b=>b.client.toLowerCase().includes(search.toLowerCase())||b.ref.toLowerCase().includes(search.toLowerCase())).sort((a,b)=>b.date.localeCompare(a.date));
  const totalExtras=(b)=>(b.extras||[]).reduce((s,x)=>s+(x.amount||0),0);
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto",display:"flex",flexDirection:"column",gap:"0.9rem"}}>
      <div style={{display:"flex",gap:"0.6rem",flexWrap:"wrap",alignItems:"center"}}>
        <input placeholder="Search client or ref…" value={search} onChange={e=>setSearch(e.target.value)} style={{flex:"1 1 200px",background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
        <span style={{fontSize:"0.68rem",color:"#2a2633"}}>{bkList.length} bookings</span>
      </div>
      {selBk?(
        <div style={{flex:1,display:"flex",flexDirection:"column",gap:"0.8rem"}}>
          <div style={{display:"flex",alignItems:"center",gap:"0.6rem"}}>
            <button onClick={()=>setSelBk(null)} style={{background:"none",border:"1px solid #1e1c26",borderRadius:"6px",color:"#4a4560",cursor:"pointer",fontSize:"0.7rem",padding:"0.3rem 0.6rem",fontFamily:"'DM Sans',sans-serif"}}>← Back</button>
            <div style={{color:"#c8c0b0",fontWeight:500,fontSize:"0.85rem"}}>{selBk.client}</div>
            <span style={{fontFamily:"monospace",fontSize:"0.66rem",color:"#c9a96e"}}>{selBk.ref}</span>
            <span style={{marginLeft:"auto",fontSize:"0.7rem",color:"#4a4560"}}>{selBk.date}</span>
          </div>
          <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"1rem"}}>
            <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
              <div style={{fontWeight:600,fontSize:"0.82rem",color:"#c8c0b0",marginBottom:"0.6rem"}}>Original Booking</div>
              <div style={{fontSize:"0.74rem",color:"#5a5060",marginBottom:"0.3rem"}}>{selBk.service}</div>
              <div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",color:"#4a4560",marginBottom:"0.2rem"}}>
                <span>{selBk.therapist||"—"}</span>
                <span style={{fontWeight:600,color:"#c9a96e"}}>K{(selBk.amount||0).toLocaleString()}</span>
              </div>
            </div>
            <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
              <div style={{fontWeight:600,fontSize:"0.82rem",color:"#c8c0b0",marginBottom:"0.6rem"}}>Add Extra Service</div>
              <div style={{display:"flex",gap:"0.4rem",marginBottom:"0.4rem"}}>
                <input value={addName} onChange={e=>setAddName(e.target.value)} placeholder="Service name" style={{flex:1,background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.7rem",color:"#e8d5b7",fontSize:"0.75rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
                <input type="number" min="0" value={addAmt} onChange={e=>setAddAmt(e.target.value)} placeholder="Amount" style={{width:"90px",background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.7rem",color:"#e8d5b7",fontSize:"0.75rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
                <button onClick={()=>{if(!addName.trim()||!addAmt||+addAmt<=0)return;const extra={name:addName.trim(),amount:+addAmt,addedAt:new Date().toISOString().slice(0,10)};setBookings(bb=>bb.map(b=>b.id===selBk.id?{...b,extras:[...(b.extras||[]),extra]}:b));setSelBk(prev=>({...prev,extras:[...(prev.extras||[]),extra]}));setAddName("");setAddAmt("");}} style={{padding:"0.48rem 0.8rem",borderRadius:"8px",border:"none",background:"#c9a96e",color:"#0d0b10",cursor:"pointer",fontWeight:600,fontSize:"0.75rem",whiteSpace:"nowrap",fontFamily:"'DM Sans',sans-serif"}}>+ Add</button>
              </div>
            </div>
          </div>
          <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
            <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.6rem"}}>
              <span style={{fontWeight:600,fontSize:"0.82rem",color:"#c8c0b0"}}>All Services</span>
              <span style={{fontSize:"0.7rem",color:"#3a3650"}}>{(selBk.extras||[]).length+1} item{(selBk.extras||[]).length+1>1?"s":""}</span>
            </div>
            <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.74rem"}}>
              <thead><tr style={{borderBottom:"1px solid #16141f"}}>
                <th style={{textAlign:"left",padding:"0.4rem 0.5rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>#</th>
                <th style={{textAlign:"left",padding:"0.4rem 0.5rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>Service</th>
                <th style={{textAlign:"right",padding:"0.4rem 0.5rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>Type</th>
                <th style={{textAlign:"right",padding:"0.4rem 0.5rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>Amount</th>
                <th style={{padding:"0.4rem 0.5rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}></th>
              </tr></thead>
              <tbody>
                <tr style={{borderBottom:"1px solid #111019"}}>
                  <td style={{padding:"0.4rem 0.5rem",color:"#2a2633"}}>1</td>
                  <td style={{padding:"0.4rem 0.5rem",color:"#a89f8c"}}>{selBk.service}</td>
                  <td style={{padding:"0.4rem 0.5rem",textAlign:"right",color:"#5cdb95",fontSize:"0.62rem"}}>Original</td>
                  <td style={{padding:"0.4rem 0.5rem",textAlign:"right",color:"#c9a96e",fontWeight:600}}>K{(selBk.amount||0).toLocaleString()}</td>
                  <td style={{padding:"0.4rem 0.5rem"}}></td>
                </tr>
                {(selBk.extras||[]).map((x,i)=>(
                  <tr key={i} style={{borderBottom:"1px solid #111019"}}>
                    <td style={{padding:"0.4rem 0.5rem",color:"#2a2633"}}>{i+2}</td>
                    <td style={{padding:"0.4rem 0.5rem",color:"#a89f8c"}}>{x.name}</td>
                    <td style={{padding:"0.4rem 0.5rem",textAlign:"right",color:"#8b9ef7",fontSize:"0.62rem"}}>Extra</td>
                    <td style={{padding:"0.4rem 0.5rem",textAlign:"right",color:"#c9a96e",fontWeight:600}}>K{x.amount.toLocaleString()}</td>
                    <td style={{padding:"0.4rem 0.5rem",textAlign:"right"}}>
                      <button onClick={()=>{setBookings(bb=>bb.map(b=>b.id===selBk.id?{...b,extras:(b.extras||[]).filter((_,j)=>j!==i)}:b));setSelBk(prev=>({...prev,extras:(prev.extras||[]).filter((_,j)=>j!==i)}));}} style={{background:"none",border:"none",color:"#ef444488",cursor:"pointer",fontSize:"0.8rem",padding:"0 0.2rem"}}>×</button>
                    </td>
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr style={{borderTop:"1px solid #1e1c26"}}>
                  <td colSpan={3} style={{padding:"0.5rem",color:"#3a3650",fontWeight:600}}>Total</td>
                  <td style={{padding:"0.5rem",textAlign:"right",fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#c9a96e",fontWeight:700}}>K{((selBk.amount||0)+totalExtras(selBk)).toLocaleString()}</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      ):(
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",overflow:"hidden"}}>
          <div style={{overflowX:"auto"}}>
            <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.74rem"}}>
              <thead><tr style={{background:"rgba(255,255,255,0.02)"}}>
                {["Ref","Client","Date","Service","Original","Extras","Total",""].map(h=><th key={h} style={{padding:"0.6rem 0.7rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.08em",whiteSpace:"nowrap",borderBottom:"1px solid #16141f"}}>{h}</th>)}
              </tr></thead>
              <tbody>
                {bkList.length===0?<tr><td colSpan={8} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No bookings match</td></tr>:bkList.map(b=>{
                  const exs=b.extras||[];
                  return(
                  <tr key={b.id}
                    onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.03)"}
                    onMouseLeave={e=>e.currentTarget.style.background="transparent"}
                  >
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}><span style={{fontFamily:"monospace",color:"#c9a96e",fontSize:"0.66rem",fontWeight:600}}>{b.ref}</span></td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}><div style={{color:"#c8c0b0",fontWeight:500,fontSize:"0.78rem"}}>{b.client}</div></td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#5a5060",fontSize:"0.68rem",whiteSpace:"nowrap"}}>{b.date}</td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#4a4560",fontSize:"0.68rem",maxWidth:"120px",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.service}</td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#c9a96e",fontWeight:600,fontSize:"0.72rem",whiteSpace:"nowrap"}}>K{(b.amount||0).toLocaleString()}</td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}>
                      {exs.length>0?<span style={{color:"#8b9ef7",fontWeight:600,fontSize:"0.72rem"}}>+{exs.length} (K{exs.reduce((s,x)=>s+(x.amount||0),0).toLocaleString()})</span>:<span style={{color:"#2a2633",fontSize:"0.65rem"}}>—</span>}
                    </td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#c9a96e",fontWeight:700,fontSize:"0.76rem",whiteSpace:"nowrap"}}>K{((b.amount||0)+exs.reduce((s,x)=>s+(x.amount||0),0)).toLocaleString()}</td>
                    <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}><button onClick={()=>setSelBk(b)} style={{padding:"0.2rem 0.5rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.65rem",whiteSpace:"nowrap"}}>Manage</button></td>
                  </tr>
                )})}
              </tbody>
            </table>
          </div>
          <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #16141f",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}>
            <span>{bkList.length} bookings</span>
            <span>Total extras: K{bkList.reduce((s,b)=>s+(b.extras||[]).reduce((s2,x)=>s2+(x.amount||0),0),0).toLocaleString()}</span>
          </div>
        </div>
      )}
    </div>
  );
}

// Smart Invoices
const INV_STATUS_META={paid:{l:"Paid",col:"#5cdb95"},unpaid:{l:"Unpaid",col:"#ef4444"},partial:{l:"Partial",col:"#ffb347"}};
function AdminInvoices({bookings,setBookings}){
  const [search,setSearch]=useState("");
  const [filterPay,setFilterPay]=useState("all");
  const [viewInv,setViewInv]=useState(null);
  const [showCreate,setShowCreate]=useState(false);
  const invoices=useMemo(()=>bookings.map(b=>{
    const extras=b.extras||[];
    const extraTotal=extras.reduce((s,x)=>s+(x.amount||0),0);
    return{...b,invId:`INV-${b.ref}`,dueDate:new Date(new Date(b.date).getTime()+7*86400000).toISOString().slice(0,10),extraTotal,grandTotal:(b.amount||0)+extraTotal,items:b.service.split(",").map(s=>s.trim()).filter(Boolean).map((s,i)=>({name:s,price:i===0?b.amount||0:0})).concat(extras.map(x=>({name:x.name,price:x.amount,extra:true})))};
  }).sort((a,b)=>b.date.localeCompare(a.date)),[bookings]);
  const filtered=invoices.filter(inv=>(filterPay==="all"||inv.payment===filterPay)&&(inv.client.toLowerCase().includes(search.toLowerCase())||inv.invId.toLowerCase().includes(search.toLowerCase())||inv.ref.toLowerCase().includes(search.toLowerCase())));
  const totalOutstanding=invoices.filter(i=>i.payment==="unpaid"||i.payment==="partial").reduce((s,i)=>s+(i.grandTotal||i.amount||0),0);
  const totalPaid=invoices.filter(i=>i.payment==="paid").reduce((s,i)=>s+(i.grandTotal||i.amount||0),0);
  const TBL={fontSize:"0.74rem",borderCollapse:"collapse",width:"100%"};
  const TH={padding:"0.6rem 0.7rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.08em",whiteSpace:"nowrap",borderBottom:"1px solid #16141f"};
  const TD={padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",whiteSpace:"nowrap"};
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto",display:"flex",flexDirection:"column",gap:"0.9rem"}}>
      <div style={{display:"flex",gap:"0.6rem",flexWrap:"wrap",alignItems:"center"}}>
        <input placeholder="Search client or invoice…" value={search} onChange={e=>setSearch(e.target.value)} style={{flex:"1 1 160px",background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
        <select value={filterPay} onChange={e=>setFilterPay(e.target.value)} style={{background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",cursor:"pointer",fontFamily:"'DM Sans',sans-serif"}}>
          <option value="all">All Statuses</option>
          {Object.entries(INV_STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}
        </select>
        <span style={{fontSize:"0.68rem",color:"#2a2633"}}>{filtered.length} invoices</span>
        <PrimaryBtn onClick={()=>setShowCreate(true)} style={{marginLeft:"auto",whiteSpace:"nowrap",padding:"0.5rem 1.1rem"}}>+ Create Invoice</PrimaryBtn>
      </div>
      {showCreate&&<InvoiceCreateModal bookings={bookings} setBookings={setBookings} onClose={()=>setShowCreate(false)} onViewInvoice={(inv)=>{setViewInv(inv);setShowCreate(false);}}/>}
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(130px,1fr))",gap:"0.6rem"}}>
        {[
          {l:"Total Invoiced",v:`K${invoices.reduce((s,i)=>s+(i.grandTotal||i.amount||0),0).toLocaleString()}`,col:"#c9a96e"},
          {l:"Collected",v:`K${totalPaid.toLocaleString()}`,col:"#5cdb95"},
          {l:"Outstanding",v:`K${totalOutstanding.toLocaleString()}`,col:"#ef4444"},
          {l:"Count",v:invoices.length,col:"#8b9ef7"}
        ].map(k=>(
          <div key={k.l} style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"10px",padding:"0.7rem 0.9rem"}}>
            <div style={{fontSize:"1.1rem",fontWeight:700,color:k.col,lineHeight:1.1}}>{typeof k.v==="number"?k.v:k.v}</div>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginTop:"0.12rem"}}>{k.l}</div>
          </div>
        ))}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",overflow:"hidden"}}>
        <div style={{overflowX:"auto"}}>
          <table style={TBL}>
            <thead><tr style={{background:"rgba(255,255,255,0.02)"}}>
              {["Invoice","Client","Date","Due","Services","Amount","Status","Method",""].map(h=><th key={h} style={TH}>{h}</th>)}
            </tr></thead>
            <tbody>
              {filtered.length===0?<tr><td colSpan={9} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No invoices match</td></tr>:filtered.map(inv=>{
                const pm=PAY_METHOD_META[inv.payMethod];
                return(
                <tr key={inv.id} style={{opacity:inv.payment==="paid"?0.75:1}}
                  onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.03)"}
                  onMouseLeave={e=>e.currentTarget.style.background="transparent"}
                >
                  <td style={TD}><span style={{fontFamily:"monospace",color:"#c9a96e",fontSize:"0.66rem",fontWeight:600}}>{inv.invId}</span></td>
                  <td style={TD}><div style={{color:"#c8c0b0",fontWeight:500,fontSize:"0.78rem"}}>{inv.client}</div>{inv.phone&&<div style={{fontSize:"0.6rem",color:"#2a2633"}}>{inv.phone}</div>}</td>
                  <td style={{...TD,color:"#5a5060",fontSize:"0.68rem"}}>{inv.date}</td>
                  <td style={{...TD,color:new Date(inv.dueDate)<new Date()&&inv.payment!=="paid"?"#ef4444":"#4a4560",fontSize:"0.68rem"}}>{inv.dueDate}</td>
                  <td style={{...TD,color:"#4a4560",fontSize:"0.68rem",maxWidth:"120px",overflow:"hidden",textOverflow:"ellipsis"}}>{inv.service}</td>
                  <td style={{...TD,color:"#c9a96e",fontWeight:700,fontSize:"0.76rem"}}>K{(inv.grandTotal||inv.amount||0).toLocaleString()}{inv.extraTotal>0&&<span style={{color:"#8b9ef7",fontWeight:400,fontSize:"0.6rem",marginLeft:"0.25rem"}}>+extras</span>}</td>
                  <td style={TD}><span style={{padding:"0.15rem 0.5rem",borderRadius:"8px",fontSize:"0.62rem",fontWeight:600,background:`${(INV_STATUS_META[inv.payment]||{}).col}15`,color:INV_STATUS_META[inv.payment]?.col||"#4a4560"}}>{INV_STATUS_META[inv.payment]?.l||inv.payment}</span></td>
                  <td style={TD}>{pm?<span style={{color:pm.c,fontSize:"0.64rem",fontWeight:500}}>{pm.l}</span>:<span style={{color:"#2a2633",fontSize:"0.6rem"}}>—</span>}</td>
                  <td style={TD}><button onClick={()=>setViewInv(inv)} style={{padding:"0.2rem 0.5rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.65rem",whiteSpace:"nowrap"}}>View</button></td>
                </tr>
              )})}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #16141f",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}>
          <span>{filtered.length} of {invoices.length} invoices</span>
          <span>Outstanding: K{totalOutstanding.toLocaleString()}</span>
        </div>
      </div>
      {viewInv&&<InvoiceDetailModal invoice={viewInv} onClose={()=>setViewInv(null)} bookings={bookings}/>}
    </div>
  );
}

function InvoiceCreateModal({bookings,setBookings,onClose,onViewInvoice}){
  const [tab,setTab]=useState("unpaid");
  const [bkSearch,setBkSearch]=useState("");
  const [selBk,setSelBk]=useState(null);
  const [mf,setMf]=useState({client:"",phone:"",email:"",service:"",amount:"",payMethod:"cash",date:new Date().toISOString().slice(0,10),status:"unpaid"});
  const [mErr,setMErr]=useState({});
  const unpaid=bookings.filter(b=>b.payment==="unpaid"||b.payment==="partial");
  const grouped=useMemo(()=>{const map={};unpaid.forEach(b=>{const k=b.client;if(!map[k])map[k]={client:k,phone:b.phone,email:b.email,bookings:[]};map[k].bookings.push(b);});return Object.values(map).sort((a,b)=>b.bookings.length-a.length);},[unpaid]);
  const filtered=grouped.filter(g=>g.client.toLowerCase().includes(bkSearch.toLowerCase()));
  const genInvoiceAndView=(bk)=>{setSelBk(bk);};
  const createManual=()=>{const e={};if(!mf.client.trim())e.client="Required";if(!mf.service.trim())e.service="Required";if(!mf.amount||+mf.amount<=0)e.amount="Required";setMErr(e);if(Object.keys(e).length)return;const nb={id:Date.now(),ref:genRef(),source:"admin",client:mf.client.trim(),phone:mf.phone,email:mf.email,service:mf.service.trim(),cat:"",therapist:"",date:mf.date,time:"",amount:+mf.amount,status:"confirmed",payment:mf.status,payMethod:mf.payMethod,note:"Manual invoice"};setBookings(bb=>[...bb,nb]);onClose();onViewInvoice({...nb,invId:`INV-${nb.ref}`,dueDate:new Date(new Date(nb.date).getTime()+7*86400000).toISOString().slice(0,10)});};
  const SI2={width:"100%",padding:"0.55rem 0.7rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.77rem"};
  return(
    <BaseModal title="Create Invoice" subtitle="Select unpaid bookings or enter details manually" onClose={onClose} wide>
      <div style={{display:"flex",gap:"0.6rem",marginBottom:"0.8rem"}}>
        {["unpaid","manual"].map(t=><button key={t} onClick={()=>{setTab(t);setSelBk(null);}} style={{flex:1,padding:"0.45rem 0",borderRadius:"8px",border:"1px solid",borderColor:tab===t?"#c9a96e":"#1e1c26",background:tab===t?"rgba(201,169,110,0.1)":"transparent",color:tab===t?"#c9a96e":"#4a4560",cursor:"pointer",fontWeight:600,fontSize:"0.75rem",fontFamily:"'DM Sans',sans-serif"}}>{t==="unpaid"?"📋 From Unpaid Bookings":"✏️ Manual Entry"}</button>)}
      </div>
      {tab==="unpaid"?(
        <div>
          <input placeholder="Search client…" value={bkSearch} onChange={e=>setBkSearch(e.target.value)} style={{width:"100%",padding:"0.5rem 0.7rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.77rem",marginBottom:"0.6rem"}}/>
          {selBk?(<div>
            <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.5rem"}}>
              <button onClick={()=>setSelBk(null)} style={{background:"none",border:"1px solid #1e1c26",borderRadius:"6px",color:"#4a4560",cursor:"pointer",fontSize:"0.7rem",padding:"0.25rem 0.5rem"}}>← Back</button>
              <span style={{fontSize:"0.68rem",color:"#2a2633"}}>INV-{selBk.ref}</span>
            </div>
            <div style={{background:"#0d0c13",borderRadius:"10px",padding:"1rem"}}>
              <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"0.6rem"}}>
                <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>{selBk.client}</div>{selBk.phone&&<div style={{fontSize:"0.68rem",color:"#4a4560"}}>{selBk.phone}</div>}</div>
                <span style={{fontWeight:700,fontSize:"1.1rem",color:"#c9a96e"}}>K{selBk.amount?.toLocaleString()}</span>
              </div>
              <div style={{fontSize:"0.72rem",color:"#5a5060",marginBottom:"0.5rem"}}>{selBk.service} · {selBk.date} · {selBk.therapist}</div>
              <div style={{display:"flex",gap:"0.4rem"}}>
                <PrimaryBtn onClick={()=>onViewInvoice({...selBk,invId:`INV-${selBk.ref}`,dueDate:new Date(new Date(selBk.date).getTime()+7*86400000).toISOString().slice(0,10)})} style={{flex:1,padding:"0.5rem",fontSize:"0.75rem"}}>📄 View Invoice</PrimaryBtn>
              </div>
            </div>
          </div>):(
          <div style={{maxHeight:"340px",overflowY:"auto",display:"flex",flexDirection:"column",gap:"0.4rem"}}>
            {filtered.length===0?<div style={{textAlign:"center",padding:"2rem",color:"#2a2633",fontSize:"0.8rem"}}>No unpaid bookings found</div>:filtered.map(g=>(
              <div key={g.client} style={{background:"#0d0c13",borderRadius:"10px",padding:"0.7rem 0.9rem"}}>
                <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.3rem"}}>
                  <div style={{fontWeight:600,color:"#c8c0b0",fontSize:"0.8rem"}}>{g.client}</div>
                  <div style={{display:"flex",gap:"0.3rem",alignItems:"center"}}>
                    <span style={{fontSize:"0.6rem",color:"#3a3650"}}>{g.bookings.length} booking{g.bookings.length>1?"s":""}</span>
                    <span style={{fontSize:"0.7rem",fontWeight:600,color:"#ef4444"}}>K{g.bookings.reduce((s,b)=>s+(b.amount||0),0).toLocaleString()}</span>
                  </div>
                </div>
                {g.bookings.map(b=><div key={b.id} onClick={()=>genInvoiceAndView(b)} style={{display:"flex",justifyContent:"space-between",alignItems:"center",padding:"0.35rem 0.5rem",borderRadius:"6px",cursor:"pointer",border:"1px solid transparent",marginBottom:"0.2rem",fontSize:"0.7rem",transition:"all 0.1s"}}
                  onMouseEnter={e=>{e.currentTarget.style.background="rgba(201,169,110,0.05)";e.currentTarget.style.borderColor="#1e1c26";}}
                  onMouseLeave={e=>{e.currentTarget.style.background="transparent";e.currentTarget.style.borderColor="transparent";}}
                >
                  <div style={{display:"flex",gap:"0.4rem",alignItems:"center"}}>
                    <span style={{fontFamily:"monospace",color:"#c9a96e",fontSize:"0.6rem"}}>{b.ref}</span>
                    <span style={{color:"#5a5060"}}>{b.date}</span>
                    <span style={{color:"#4a4560",maxWidth:"100px",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.service}</span>
                  </div>
                  <span style={{fontWeight:600,color:"#c9a96e"}}>K{b.amount?.toLocaleString()}</span>
                </div>)}
              </div>
            ))}
          </div>)}
        </div>
      ):(
        <div>
          <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.6rem",marginBottom:"0.6rem"}}>
            <div>
              <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Client Name</div>
              <input value={mf.client} onChange={e=>setMf(f=>({...f,client:e.target.value}))} placeholder="e.g. Mary Banda" style={{...SI2,borderColor:mErr.client?"#ef444455":"#1e1c26"}}/>
              {mErr.client&&<div style={{fontSize:"0.6rem",color:"#ef4444",marginTop:"0.15rem"}}>{mErr.client}</div>}
            </div>
            <div>
              <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Phone</div>
              <input value={mf.phone} onChange={e=>setMf(f=>({...f,phone:e.target.value}))} placeholder="+260 97 XXX" style={SI2}/>
            </div>
          </div>
          <div style={{marginBottom:"0.6rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Email</div>
            <input value={mf.email} onChange={e=>setMf(f=>({...f,email:e.target.value}))} placeholder="client@email.com" style={SI2}/>
          </div>
          <div style={{marginBottom:"0.6rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Service(s)</div>
            <input value={mf.service} onChange={e=>setMf(f=>({...f,service:e.target.value}))} placeholder="e.g. Hot Stone Massage, Facial" style={{...SI2,borderColor:mErr.service?"#ef444455":"#1e1c26"}}/>
            {mErr.service&&<div style={{fontSize:"0.6rem",color:"#ef4444",marginTop:"0.15rem"}}>{mErr.service}</div>}
          </div>
          <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:"0.6rem",marginBottom:"0.6rem"}}>
            <div>
              <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Amount (K)</div>
              <input type="number" min="0" value={mf.amount} onChange={e=>setMf(f=>({...f,amount:e.target.value}))} placeholder="0" style={{...SI2,borderColor:mErr.amount?"#ef444455":"#1e1c26"}}/>
              {mErr.amount&&<div style={{fontSize:"0.6rem",color:"#ef4444",marginTop:"0.15rem"}}>{mErr.amount}</div>}
            </div>
            <div>
              <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Date</div>
              <input type="date" value={mf.date} onChange={e=>setMf(f=>({...f,date:e.target.value}))} style={SI2}/>
            </div>
            <div>
              <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Status</div>
              <select value={mf.status} onChange={e=>setMf(f=>({...f,status:e.target.value}))} style={{...SI2,cursor:"pointer"}}>
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
              </select>
            </div>
          </div>
          {mf.status==="paid"||mf.status==="partial"?<div style={{marginBottom:"0.6rem"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em",marginBottom:"0.25rem"}}>Payment Method</div>
            <select value={mf.payMethod} onChange={e=>setMf(f=>({...f,payMethod:e.target.value}))} style={{...SI2,cursor:"pointer"}}>
              {Object.entries(PAY_METHOD_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}
            </select>
          </div>:null}
          <PrimaryBtn onClick={createManual} style={{width:"100%",padding:"0.65rem",fontSize:"0.8rem"}}>+ Generate Invoice</PrimaryBtn>
        </div>
      )}
    </BaseModal>
  );
}

function InvoiceDetailModal({invoice,onClose,bookings}){
  const pm=PAY_METHOD_META[invoice.payMethod];
  const totalItems=invoice.service.split(",").map(s=>s.trim()).filter(Boolean);
  const extras=invoice.extras||[];
  const itemPrice=totalItems.length>1?Math.round((invoice.amount||0)/totalItems.length):invoice.amount||0;
  const invDate=new Date(invoice.date);
  const dueDate=new Date(invDate.getTime()+7*86400000);
  const daysOverdue=dueDate<new Date()&&invoice.payment!=="paid"?Math.floor((new Date()-dueDate)/86400000):0;
  const clientBookings=bookings.filter(b=>b.client===invoice.client).length;
  return(
    <BaseModal title={`Invoice ${invoice.invId}`} subtitle={`${invoice.client} · ${invoice.date}`} onClose={onClose}>
      <div style={{background:"#0d0c13",borderRadius:"10px",padding:"1rem",marginBottom:"0.8rem"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"0.8rem"}}>
          <div>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.15rem"}}>Billed To</div>
            <div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>{invoice.client}</div>
            {invoice.phone&&<div style={{fontSize:"0.7rem",color:"#4a4560"}}>{invoice.phone}</div>}
            {invoice.email&&<div style={{fontSize:"0.7rem",color:"#4a4560"}}>{invoice.email}</div>}
          </div>
          <div style={{textAlign:"right"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.15rem"}}>Status</div>
            <span style={{padding:"0.2rem 0.6rem",borderRadius:"8px",fontSize:"0.68rem",fontWeight:600,background:`${(INV_STATUS_META[invoice.payment]||{}).col}18`,color:INV_STATUS_META[invoice.payment]?.col||"#4a4560"}}>{INV_STATUS_META[invoice.payment]?.l||invoice.payment}</span>
            {daysOverdue>0&&<div style={{fontSize:"0.6rem",color:"#ef4444",marginTop:"0.25rem"}}>{daysOverdue} day{daysOverdue>1?"s":""} overdue</div>}
          </div>
        </div>
        <div style={{display:"flex",gap:"1.5rem",fontSize:"0.7rem",color:"#4a4560",padding:"0.5rem 0",borderTop:"1px solid #16141f",borderBottom:"1px solid #16141f",marginBottom:"0.6rem"}}>
          <div><span style={{color:"#3a3650"}}>Issue:</span> {invoice.date}</div>
          <div><span style={{color:"#3a3650"}}>Due:</span> {invoice.dueDate}</div>
          <div><span style={{color:"#3a3650"}}>Ref:</span> {invoice.ref}</div>
          <div><span style={{color:"#3a3650"}}>Bookings:</span> {clientBookings}</div>
        </div>
        <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.75rem",marginBottom:"0.8rem"}}>
          <thead><tr style={{borderBottom:"1px solid #1e1c26"}}>
            <th style={{textAlign:"left",padding:"0.45rem 0.4rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>Service</th>
            <th style={{textAlign:"right",padding:"0.45rem 0.4rem",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.07em"}}>Amount</th>
          </tr></thead>
          <tbody>
            {totalItems.map((s,i)=><tr key={i} style={{borderBottom:"1px solid #16141f"}}>
              <td style={{padding:"0.4rem",color:"#a89f8c"}}>{s}</td>
              <td style={{padding:"0.4rem",textAlign:"right",color:"#c9a96e",fontWeight:600}}>K{itemPrice.toLocaleString()}</td>
            </tr>)}
            {extras.map((x,i)=><tr key={`ex-${i}`} style={{borderBottom:"1px solid #16141f"}}>
              <td style={{padding:"0.4rem",color:"#a89f8c"}}>{x.name} <span style={{fontSize:"0.58rem",color:"#8b9ef7"}}>(extra)</span></td>
              <td style={{padding:"0.4rem",textAlign:"right",color:"#8b9ef7",fontWeight:600}}>K{x.amount.toLocaleString()}</td>
            </tr>)}
            {invoice.note&&<tr><td colSpan={2} style={{padding:"0.4rem",fontSize:"0.65rem",color:"#2a2633",fontStyle:"italic"}}>📝 {invoice.note}</td></tr>}
          </tbody>
        </table>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",borderTop:"1px solid #1e1c26",padding:"0.6rem 0.4rem 0"}}>
          {pm&&<div style={{display:"flex",alignItems:"center",gap:"0.3rem",fontSize:"0.7rem"}}><span style={{width:"8px",height:"8px",borderRadius:"50%",background:pm.c,display:"inline-block"}}/>{pm.l}</div>}
          <div style={{textAlign:"right"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em"}}>Total</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.5rem",color:"#c9a96e",fontWeight:600}}>K{(invoice.grandTotal||invoice.amount||0).toLocaleString()}</div>
          </div>
        </div>
      </div>
      <div style={{display:"flex",gap:"0.6rem"}}>
        <GhostBtn onClick={()=>window.print()} style={{flex:1,textAlign:"center"}}>🖨 Print</GhostBtn>
        <GhostBtn onClick={onClose} style={{flex:1,textAlign:"center"}}>Close</GhostBtn>
      </div>
    </BaseModal>
  );
}

function NotifPanel({notifications,setNotifications,onClose}){
  return(
    <div style={{position:"fixed",top:"100px",right:"12px",width:"300px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"16px",boxShadow:"0 24px 80px rgba(0,0,0,0.5)",zIndex:150,overflow:"hidden",animation:"slideIn 0.2s ease both"}}>
      <div style={{padding:"0.85rem 1.1rem",borderBottom:"1px solid #16141f",display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <span style={{fontFamily:"'Cormorant Garamond',serif",color:"#e8d5b7",fontSize:"0.92rem",fontWeight:600}}>Notifications</span>
        <div style={{display:"flex",gap:"0.6rem",alignItems:"center"}}>
          <button onClick={()=>setNotifications(ns=>ns.map(n=>({...n,read:true})))} style={{background:"none",border:"none",fontSize:"0.65rem",color:"#c9a96e",cursor:"pointer",padding:0,fontFamily:"'DM Sans',sans-serif"}}>Mark all read</button>
          <button onClick={onClose} style={{background:"none",border:"none",color:"#4a4560",fontSize:"1.2rem",cursor:"pointer",lineHeight:1,padding:0,opacity:0.6}}>×</button>
        </div>
      </div>
      <div style={{maxHeight:"340px",overflowY:"auto"}}>
        {notifications.map(n=>(
          <div key={n.id} onClick={()=>setNotifications(ns=>ns.map(x=>x.id===n.id?{...x,read:true}:x))} style={{display:"flex",gap:"0.6rem",padding:"0.7rem 1.1rem",borderBottom:"1px solid #111019",background:n.read?"transparent":"rgba(201,169,110,0.03)",cursor:"pointer",transition:"background 0.15s"}}
            onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.06)"}
            onMouseLeave={e=>e.currentTarget.style.background=n.read?"transparent":"rgba(201,169,110,0.03)"}
          >
            <div style={{width:"6px",height:"6px",borderRadius:"50%",background:n.read?"#2a2633":n.type==="booking"?"#5cdb95":n.type==="payment"?"#c9a96e":"#ef4444",marginTop:"5px",flexShrink:0}}/>
            <div><div style={{fontSize:"0.74rem",color:n.read?"#4a4560":"#a89f8c",lineHeight:1.4}}>{n.msg}</div><div style={{fontSize:"0.6rem",color:"#2a2633",marginTop:"0.12rem"}}>{n.time}</div></div>
          </div>
        ))}
      </div>
    </div>
  );
}

// Services Management
function AdminServices({services,setServices,currentAdmin}){
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const [catF,setCatF]=useState("All");
  const cats=["All",...new Set(services.map(s=>s.cat))];
  const filtered=catF==="All"?services:services.filter(s=>s.cat===catF);
  const save=(fd)=>{const bd=currentAdmin?.role!=="superadmin"?{...fd,branch:currentAdmin.branch}:fd;if(editing)setServices(ss=>ss.map(s=>s.name===editing.name?{...s,...bd}:s));else setServices(ss=>[...ss,{...bd,id:Date.now(),price:+bd.price}]);setShowForm(false);setEditing(null);};
  const del=(svc)=>{setServices(ss=>ss.filter(s=>s.name!==svc.name));setDeleting(null);};
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto"}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"1.2rem",flexWrap:"wrap",gap:"0.6rem"}}>
        <div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>Service Catalog</h3><p style={{margin:"0.15rem 0 0",fontSize:"0.7rem",color:"#3a3650"}}>{services.length} services · changes reflect immediately in client booking</p></div>
        <div style={{display:"flex",gap:"0.5rem",alignItems:"center"}}>
          <select value={catF} onChange={e=>setCatF(e.target.value)} style={{background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",cursor:"pointer",fontFamily:"'DM Sans',sans-serif"}}>{cats.map(c=><option key={c} value={c}>{c}</option>)}</select>
          <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}} style={{whiteSpace:"nowrap",padding:"0.55rem 1.2rem"}}>+ Add Service</PrimaryBtn>
        </div>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(240px,1fr))",gap:"0.75rem"}}>
        {filtered.map(s=>{
          const catMeta=CAT_META[s.cat]||{icon:"📋",col:"#c9a96e",bg:"rgba(201,169,110,0.08)"};
          return(
            <div key={s.name} style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",position:"relative",overflow:"hidden"}}>
              <div style={{position:"absolute",top:0,left:0,right:0,height:"3px",background:`linear-gradient(90deg,${catMeta.col},transparent)`}}/>
              <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"0.6rem"}}>
                <div style={{display:"flex",gap:"0.55rem",alignItems:"center",flex:1,minWidth:0}}>
                  <div style={{width:"32px",height:"32px",borderRadius:"8px",background:catMeta.bg,display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.85rem",flexShrink:0}}>{catMeta.icon}</div>
                  <div style={{flex:1,minWidth:0}}><div style={{fontWeight:500,color:"#c8c0b0",fontSize:"0.8rem",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{s.name}</div><div style={{fontSize:"0.6rem",color:catMeta.col,textTransform:"uppercase",letterSpacing:"0.07em",marginTop:"0.05rem"}}>{s.cat}</div></div>
                </div>
                <div style={{fontWeight:700,color:"#c9a96e",fontSize:"0.85rem",flexShrink:0,marginLeft:"0.5rem"}}>K{s.price.toLocaleString()}</div>
              </div>
              <div style={{display:"flex",gap:"0.25rem",justifyContent:"flex-end"}}>
                <button onClick={()=>{setEditing(s);setShowForm(true);}} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #2a2633",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.62rem"}}>✏ Edit</button>
                <button onClick={()=>setDeleting(s)} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #ff4d6d33",background:"transparent",color:"#ff4d6d",cursor:"pointer",fontSize:"0.62rem"}}>🗑</button>
              </div>
            </div>
          );
        })}
        {filtered.length===0&&<div style={{gridColumn:"1/-1",textAlign:"center",padding:"3rem",color:"#2a2633",fontSize:"0.85rem"}}>No services in this category</div>}
      </div>
      {showForm&&<ServiceFormModal service={editing} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="service" name={deleting.name} onConfirm={()=>del(deleting)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

function ServiceFormModal({service,onSave,onClose}){
  const empty={name:"",cat:"Massage",price:0};
  const [form,setForm]=useState(service?{name:service.name,cat:service.cat,price:service.price}:empty);
  const [errors,setErrors]=useState({});
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));
  const validate=()=>{const e={};if(!form.name.trim())e.name="Required";if(!form.cat)e.cat="Required";if(!form.price||+form.price<=0)e.price="Required";setErrors(e);return!Object.keys(e).length;};
  return(
    <BaseModal title={service?`Edit — ${service.name}`:"Add New Service"} subtitle="Manage your service catalog" onClose={onClose}>
      <FField label="Service Name" error={errors.name}><input style={{...SI,borderColor:errors.name?"#ef444455":"#1e1c26"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="e.g. Hot Stone Massage"/></FField>
      <FField label="Category" error={errors.cat}><select style={{...SI,cursor:"pointer"}} value={form.cat} onChange={e=>upd("cat",e.target.value)}>{[...new Set(SERVICES_LIST.map(s=>s.cat))].map(c=><option key={c} value={c}>{c}</option>)}</select></FField>
      <FField label="Price (K)" error={errors.price}><input style={{...SI,borderColor:errors.price?"#ef444455":"#1e1c26"}} type="number" min="0" value={form.price} onChange={e=>upd("price",e.target.value)}/></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.5rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave({...form,price:+form.price});}} style={{flex:2}}>{service?"Save Changes":"Add Service"}</PrimaryBtn></div>
    </BaseModal>
  );
}

// Inventory Management
function AdminInventory({products,setProducts,currentAdmin}){
  const [search,setSearch]=useState("");
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const filtered=products.filter(p=>p.name.toLowerCase().includes(search.toLowerCase())||p.cat.toLowerCase().includes(search.toLowerCase()));
  const save=(fd)=>{const bd=currentAdmin?.role!=="superadmin"?{...fd,branch:currentAdmin.branch}:fd;if(editing)setProducts(pp=>pp.map(p=>p.id===editing.id?{...p,...bd}:p));else setProducts(pp=>[...pp,{...bd,id:Date.now()}]);setShowForm(false);setEditing(null);};
  const del=(id)=>{setProducts(pp=>pp.filter(p=>p.id!==id));setDeleting(null);};
  const LOW_THRESH=15;
  const lowCount=products.filter(p=>p.stock<LOW_THRESH).length;
  const highCount=products.length-lowCount;
  const pieData=[
    {name:"Low Stock",value:lowCount,color:"#ef4444"},
    {name:"Healthy",value:highCount,color:"#5cdb95"}
  ].filter(d=>d.value>0);
  const totalValue=products.reduce((s,p)=>s+p.price*p.stock,0);
  const catData=Object.entries(products.reduce((m,p)=>{m[p.cat]=(m[p.cat]||0)+1;return m;},{})).map(([k,v])=>({category:k,count:v}));
  const barColors=["#c9a96e","#8b9ef7","#5cdb95","#f472b6","#fbbf24","#a78bfa"];
  return(
    <div style={{padding:"1.3rem 1.3rem 1.3rem 1.3rem",overflowY:"auto",height:"100%",display:"flex",flexDirection:"column",gap:"1rem"}}>
      <div style={{display:"flex",gap:"0.9rem",flexWrap:"wrap"}}>
        <div style={{flex:"1 1 140px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"0.9rem 1rem",minWidth:"120px"}}>
          <div style={{fontSize:"0.58rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.3rem"}}>Total Products</div>
          <div style={{fontSize:"1.5rem",fontWeight:700,color:"#e8d5b7"}}>{products.length}</div>
        </div>
        <div style={{flex:"1 1 140px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"0.9rem 1rem",minWidth:"120px"}}>
          <div style={{fontSize:"0.58rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.3rem"}}>Low Stock</div>
          <div style={{fontSize:"1.5rem",fontWeight:700,color:lowCount>0?"#ef4444":"#5cdb95"}}>{lowCount}</div>
        </div>
        <div style={{flex:"1 1 140px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"0.9rem 1rem",minWidth:"120px"}}>
          <div style={{fontSize:"0.58rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.3rem"}}>Stock Value</div>
          <div style={{fontSize:"1.5rem",fontWeight:700,color:"#c9a96e"}}>K{totalValue.toLocaleString()}</div>
        </div>
      </div>
      <div style={{display:"flex",gap:"0.9rem",flexWrap:"wrap"}}>
        {pieData.length>0&&(
          <div style={{flex:"1 1 260px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",minWidth:"200px"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.5rem"}}>Stock Health</div>
            <ResponsiveContainer width="100%" height={180}>
              <PieChart>
                <Pie data={pieData} cx="50%" cy="50%" outerRadius={65} dataKey="value" label={({name,value})=>`${name} ${value}`}>
                  {pieData.map((e,i)=><Cell key={i} fill={e.color}/>)}
                </Pie>
                <Tooltip/>
              </PieChart>
            </ResponsiveContainer>
          </div>
        )}
        {catData.length>0&&(
          <div style={{flex:"1 1 300px",background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem",minWidth:"200px"}}>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginBottom:"0.5rem"}}>Products by Category</div>
            <ResponsiveContainer width="100%" height={180}>
              <BarChart data={catData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#1e1c26"/>
                <XAxis dataKey="category" tick={{fill:"#4a4560",fontSize:10}} axisLine={{stroke:"#1e1c26"}}/>
                <YAxis allowDecimals={false} tick={{fill:"#4a4560",fontSize:10}} axisLine={{stroke:"#1e1c26"}}/>
                <Tooltip contentStyle={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"8px",fontSize:"0.72rem"}}/>
                <Bar dataKey="count" radius={[4,4,0,0]}>
                  {catData.map((_,i)=><Cell key={i} fill={barColors[i%barColors.length]}/>)}
                </Bar>
              </BarChart>
            </ResponsiveContainer>
          </div>
        )}
      </div>
      <div style={{display:"flex",gap:"0.6rem",flexWrap:"wrap",alignItems:"center"}}>
        <input placeholder="Search products…" value={search} onChange={e=>setSearch(e.target.value)} style={{flex:"1 1 200px",background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"}}/>
        <span style={{fontSize:"0.68rem",color:"#2a2633"}}>{filtered.length} products</span>
        <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}} style={{marginLeft:"auto",whiteSpace:"nowrap",padding:"0.5rem 1.1rem"}}>+ Add Product</PrimaryBtn>
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",overflow:"hidden",flexShrink:0}}>
        <div style={{overflowX:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.74rem"}}>
            <thead><tr style={{background:"rgba(255,255,255,0.02)"}}>
              {["Product","Category","Branch","Price","Stock","Value",""].map(h=><th key={h} style={{padding:"0.6rem 0.7rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.08em",whiteSpace:"nowrap",borderBottom:"1px solid #16141f"}}>{h}</th>)}
            </tr></thead>
            <tbody>
              {filtered.length===0?<tr><td colSpan={7} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No products match</td></tr>:filtered.map(p=>(
                <tr key={p.id}
                  onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.03)"}
                  onMouseLeave={e=>e.currentTarget.style.background="transparent"}
                >
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#c8c0b0",fontWeight:500}}>{p.name}</td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#4a4560",fontSize:"0.68rem"}}>{p.cat}</td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}><span style={{fontSize:"0.62rem",padding:"0.12rem 0.45rem",borderRadius:"8px",fontWeight:600,background:p.branch==="woodlands"?"rgba(201,169,110,0.12)":"rgba(92,219,149,0.12)",color:p.branch==="woodlands"?"#c9a96e":"#5cdb95"}}>{BRANCHES[p.branch]||p.branch}</span></td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#c9a96e",fontWeight:600}}>K{p.price.toLocaleString()}</td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019"}}><span style={{color:p.stock<LOW_THRESH?"#ef4444":"#5cdb95",fontWeight:600}}>{p.stock}</span>{p.stock<LOW_THRESH&&<span style={{fontSize:"0.58rem",color:"#ef4444",marginLeft:"0.3rem"}}>low</span>}</td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",color:"#8b9ef7",fontWeight:600}}>K{(p.price*p.stock).toLocaleString()}</td>
                  <td style={{padding:"0.5rem 0.7rem",borderBottom:"1px solid #111019",textAlign:"right",whiteSpace:"nowrap"}}>
                    <button onClick={()=>{setEditing(p);setShowForm(true);}} style={{padding:"0.2rem 0.5rem",borderRadius:"6px",border:"1px solid #1e1c26",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.65rem",marginRight:"0.3rem"}}>Edit</button>
                    <button onClick={()=>setDeleting(p)} style={{padding:"0.2rem 0.5rem",borderRadius:"6px",border:"1px solid #ff4d6d33",background:"transparent",color:"#ff4d6d",cursor:"pointer",fontSize:"0.65rem"}}>×</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #16141f",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}>
          <span>{filtered.length} of {products.length} products</span>
          <span>Total stock value: K{totalValue.toLocaleString()}</span>
        </div>
      </div>
      {showForm&&(
        <BaseModal title={editing?`Edit — ${editing.name}`:"New Product"} subtitle="Add or update inventory item" onClose={()=>{setShowForm(false);setEditing(null);}}>
          <ProductForm initial={editing} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}} currentAdmin={currentAdmin}/>
        </BaseModal>
      )}
      {deleting&&<DeleteConfirm what="product" name={deleting.name} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}
function ProductForm({initial,onSave,onClose,currentAdmin}){
  const empty={name:"",cat:"Skincare",price:0,stock:0,branch:"woodlands"};
  const [form,setForm]=useState(initial?{name:initial.name,cat:initial.cat,price:initial.price,stock:initial.stock,branch:initial.branch||"woodlands"}:empty);
  const [err,setErr]=useState({});
  const IS={width:"100%",padding:"0.55rem 0.7rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.77rem"};
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));
  const validate=()=>{const e={};if(!form.name.trim())e.name="Required";if(!form.price||+form.price<=0)e.price="Required";setErr(e);return!Object.keys(e).length;};
  return(
    <div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem",marginBottom:"0.6rem"}}>
        <FField label="Product Name" error={err.name}><input style={{...IS,borderColor:err.name?"#ef444455":"#1e1c26"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="e.g. Lavender Candle"/></FField>
        <FField label="Category"><select style={{...IS,cursor:"pointer"}} value={form.cat} onChange={e=>upd("cat",e.target.value)}>
          {["Skincare","Candles","Oils","Bath","Accessories","Wellness"].map(c=><option key={c} value={c}>{c}</option>)}
        </select></FField>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem",marginBottom:"0.6rem"}}>
        <FField label="Price (K)" error={err.price}><input type="number" min="0" value={form.price} onChange={e=>upd("price",+e.target.value)} style={{...IS,borderColor:err.price?"#ef444455":"#1e1c26"}}/></FField>
        <FField label="Stock Quantity"><input type="number" min="0" value={form.stock} onChange={e=>upd("stock",+e.target.value)} style={IS}/></FField>
      </div>
      {currentAdmin?.role==="superadmin"&&<div style={{marginBottom:"0.6rem"}}>
        <FField label="Branch"><select style={{...IS,cursor:"pointer"}} value={form.branch} onChange={e=>upd("branch",e.target.value)}>
          {Object.entries(BRANCHES).map(([k,v])=><option key={k} value={k}>{v}</option>)}
        </select></FField>
      </div>}
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.5rem"}}>
        <GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn>
        <PrimaryBtn onClick={()=>{if(validate())onSave(form);}} style={{flex:2}}>{initial?"Save Changes":"Add Product"}</PrimaryBtn>
      </div>
    </div>
  );
}

// Gift Cards Manager
function AdminGiftCards({giftCards,setGiftCards,currentAdmin}){
  const [sel,setSel]=useState(null);
  const filtered=giftCards.filter(g=>currentAdmin?.role==="superadmin"||g.branch===currentAdmin?.branch);
  const active=filtered.filter(g=>g.status==="active").length;
  const redeemed=filtered.filter(g=>g.status==="redeemed").length;
  const markRedeemed=(id)=>{setGiftCards(gg=>gg.map(g=>g.id===id?{...g,status:"redeemed",redeemedAt:new Date().toISOString().slice(0,10)}:g));setSel(null);};
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto"}}>
      <div style={{marginBottom:"1.2rem"}}>
        <h3 style={{margin:"0 0 0.15rem",fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>🎁 Gift Cards</h3>
        <p style={{margin:0,fontSize:"0.7rem",color:"#3a3650"}}>Manage issued gift cards — {filtered.length} total ({active} active, {redeemed} redeemed)</p>
      </div>
      {filtered.length===0?<div style={{textAlign:"center",padding:"3rem",color:"#2a2633",fontSize:"0.85rem"}}>No gift cards yet. Clients can purchase gift cards during booking.</div>
      :<div style={{display:"flex",flexDirection:"column",gap:"0.5rem"}}>
        {filtered.map(g=>{
          const act=g.status==="active";
          return(<div key={g.id} onClick={()=>setSel(g)} style={{background:"#0f0d14",border:`1px solid ${act?"rgba(255,158,181,0.2)":"#1e1c26"}`,borderRadius:"12px",padding:"0.8rem 1rem",cursor:"pointer",transition:"all 0.1s"}}>
            <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",gap:"0.5rem"}}>
              <div style={{flex:1,minWidth:0}}>
                <div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.85rem",display:"flex",alignItems:"center",gap:"0.4rem"}}>
                  <span style={{fontFamily:"'Courier New',monospace",color:"#ff9eb5",letterSpacing:"0.1em"}}>{g.code}</span>
                  <span style={{fontSize:"0.6rem",padding:"0.1rem 0.4rem",borderRadius:"6px",fontWeight:600,background:act?"rgba(92,219,149,0.12)":"rgba(255,77,109,0.12)",color:act?"#5cdb95":"#ff4d6d"}}>{act?"Active":"Redeemed"}</span>
                </div>
                <div style={{fontSize:"0.7rem",color:"#4a4560",marginTop:"0.15rem"}}>For: <span style={{color:"#8a7f70"}}>{g.recipient||"Unnamed"}</span> — By: <span style={{color:"#8a7f70"}}>{g.buyer}</span></div>
              </div>
              <div style={{textAlign:"right",flexShrink:0}}>
                <div style={{fontFamily:"'Cormorant Garamond',serif",color:"#c9a96e",fontSize:"1.1rem",fontWeight:600}}>K{g.s.toLocaleString()}</div>
                <div style={{fontSize:"0.58rem",color:"#3a3650"}}>{g.created}</div>
              </div>
            </div>
          </div>);
        })}
      </div>}
      {sel&&<div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.7)",backdropFilter:"blur(8px)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:300,padding:"1rem"}}>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"18px",padding:"2rem",width:"100%",maxWidth:"400px"}}>
          <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"1rem"}}>
            <div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.2rem",color:"#e8d5b7"}}>🎁 {sel.code}</h3></div>
            <button onClick={()=>setSel(null)} style={{background:"none",border:"none",color:"#4a4560",fontSize:"1.5rem",cursor:"pointer",lineHeight:1,padding:0}}>×</button>
          </div>
          <div style={{display:"flex",flexDirection:"column",gap:"0.4rem",fontSize:"0.78rem",marginBottom:"1rem"}}>
            <div><span style={{color:"#4a4560"}}>Status:</span> <span style={{color:sel.status==="active"?"#5cdb95":"#ff4d6d",fontWeight:600}}>{sel.status}</span></div>
            <div><span style={{color:"#4a4560"}}>Value:</span> <span style={{color:"#c9a96e",fontWeight:600}}>K{sel.s.toLocaleString()}</span></div>
            <div><span style={{color:"#4a4560"}}>Recipient:</span> <span style={{color:"#e8d5b7"}}>{sel.recipient||"—"}</span></div>
            <div><span style={{color:"#4a4560"}}>Buyer:</span> <span style={{color:"#e8d5b7"}}>{sel.buyer}</span></div>
            <div><span style={{color:"#4a4560"}}>Created:</span> <span style={{color:"#8a7f70"}}>{sel.created}</span></div>
            {sel.message&&<div style={{background:"rgba(255,158,181,0.04)",border:"1px solid rgba(255,158,181,0.1)",borderRadius:"8px",padding:"0.5rem 0.7rem",marginTop:"0.2rem"}}><div style={{fontSize:"0.6rem",color:"#4a4560",marginBottom:"0.15rem"}}>MESSAGE</div><div style={{color:"#ff9eb5",fontStyle:"italic",fontSize:"0.78rem"}}>"{sel.message}"</div></div>}
            {sel.redeemedAt&&<div><span style={{color:"#4a4560"}}>Redeemed:</span> <span style={{color:"#8a7f70"}}>{sel.redeemedAt}</span></div>}
            <div><span style={{color:"#4a4560"}}>Booking:</span> <span style={{color:"#8b9ef7"}}>{sel.bookingRef||"—"}</span></div>
            <div><span style={{color:"#4a4560"}}>Branch:</span> <span style={{color:"#8a7f70"}}>{BRANCHES[sel.branch]||sel.branch}</span></div>
          </div>
          {sel.status==="active"&&<PrimaryBtn onClick={()=>markRedeemed(sel.id)} style={{width:"100%"}}>Mark as Redeemed</PrimaryBtn>}
          <GhostBtn onClick={()=>setSel(null)} style={{width:"100%",marginTop:"0.5rem"}}>Close</GhostBtn>
        </div>
      </div>}
    </div>
  );
}

// Appearance Manager
function AdminAppearance({heroImageUrl,setHeroImageUrl}){
  const [urlInput,setUrlInput]=useState(heroImageUrl||"");
  const seedGallery=[
    {id:1,url:"https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1600&q=80",label:"Spa Candles"},
    {id:2,url:"https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?auto=format&fit=crop&w=1600&q=80",label:"Massage Room"},
    {id:3,url:"https://images.unsplash.com/photo-1600334123748-f59f43e5c4f6?auto=format&fit=crop&w=1600&q=80",label:"Flower Petals"},
    {id:4,url:"https://images.unsplash.com/photo-1560750588-73207b31ef5c?auto=format&fit=crop&w=1600&q=80",label:"Essential Oils"},
    {id:5,url:"https://images.unsplash.com/photo-1560750588-73207b31ef5c?auto=format&fit=crop&w=1600&q=80",label:"Towel Fold"},
    {id:6,url:"https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1600&q=80",label:"Stone Massage"},
  ];
  const [gallery,setGallery]=useState(()=>{
    try{const s=localStorage.getItem("ngalula_gallery");return s?JSON.parse(s):seedGallery;}catch{return seedGallery;}
  });
  useEffect(()=>{try{localStorage.setItem("ngalula_gallery",JSON.stringify(gallery));}catch{}},[gallery]);
  const [uploading,setUploading]=useState(false);
  const applyImg=(url)=>{setHeroImageUrl(url);setUrlInput(url);};
  const handleFile=(e)=>{const file=e.target.files?.[0];if(!file)return;setUploading(true);const reader=new FileReader();reader.onload=()=>{const dataUrl=reader.result;setGallery(gg=>[...gg,{id:Date.now(),url:dataUrl,label:file.name.split('.')[0]}]);applyImg(dataUrl);setUploading(false);};reader.readAsDataURL(file);};
  const removeFromGallery=(id)=>{setGallery(gg=>gg.filter(g=>g.id!==id));};
  const PRESETS=["https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1600&q=80","https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?auto=format&fit=crop&w=1600&q=80","https://images.unsplash.com/photo-1600334123748-f59f43e5c4f6?auto=format&fit=crop&w=1600&q=80","https://images.unsplash.com/photo-1560750588-73207b31ef5c?auto=format&fit=crop&w=1600&q=80"];
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto"}}>
      <div style={{marginBottom:"1.2rem"}}>
        <h3 style={{margin:"0 0 0.15rem",fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>Appearance Manager</h3>
        <p style={{margin:0,fontSize:"0.7rem",color:"#3a3650"}}>Upload images, manage gallery, and set the hero banner</p>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"1.2rem",marginBottom:"1.2rem"}}>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem"}}>
          <div style={{fontWeight:600,fontSize:"0.82rem",marginBottom:"0.6rem",color:"#c8c0b0"}}>Current Hero Banner</div>
          <div style={{borderRadius:"10px",overflow:"hidden",border:"1px solid #1e1c26",aspectRatio:"16/9",background:"#0c0b11",display:"flex",alignItems:"center",justifyContent:"center"}}>
            {heroImageUrl?<img src={heroImageUrl} style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>:<span style={{color:"#2a2633",fontSize:"0.8rem"}}>No image set</span>}
          </div>
        </div>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem"}}>
          <div style={{fontWeight:600,fontSize:"0.82rem",marginBottom:"0.6rem",color:"#c8c0b0"}}>Upload or Paste URL</div>
          <div style={{display:"flex",gap:"0.5rem",marginBottom:"0.8rem"}}>
            <input value={urlInput} onChange={e=>setUrlInput(e.target.value)} placeholder="Paste image URL…" style={{flex:1,padding:"0.6rem 0.8rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.78rem"}}/>
            <button onClick={()=>urlInput.trim()&&applyImg(urlInput.trim())} style={{padding:"0.6rem 1rem",borderRadius:"8px",border:"none",background:"#c9a96e",color:"#0d0b10",cursor:"pointer",fontWeight:600,fontSize:"0.75rem",whiteSpace:"nowrap"}}>Apply</button>
          </div>
          <div style={{border:"1px dashed #2a2633",borderRadius:"10px",padding:"1rem",textAlign:"center",position:"relative",cursor:"pointer"}}
            onDragOver={e=>{e.preventDefault();e.currentTarget.style.borderColor="#c9a96e";}}
            onDragLeave={e=>{e.currentTarget.style.borderColor="#2a2633";}}
            onDrop={e=>{e.preventDefault();const file=e.dataTransfer.files?.[0];if(file){const r=new FileReader();r.onload=()=>{const d=r.result;setGallery(gg=>[...gg,{id:Date.now(),url:d,label:file.name.split('.')[0]}]);applyImg(d);};r.readAsDataURL(file);}}}
          >
            <input type="file" accept="image/*" onChange={handleFile} style={{position:"absolute",inset:0,opacity:0,cursor:"pointer"}}/>
            <div style={{fontSize:"1.3rem",marginBottom:"0.3rem"}}>{uploading?"⏳":"📁"}</div>
            <div style={{fontSize:"0.72rem",color:"#4a4560"}}>{uploading?"Uploading…":"Drop an image or click to upload"}</div>
            <div style={{fontSize:"0.6rem",color:"#2a2633",marginTop:"0.15rem"}}>PNG, JPG, WEBP</div>
          </div>
        </div>
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.8rem"}}>
          <div style={{fontWeight:600,fontSize:"0.82rem",color:"#c8c0b0"}}>Image Gallery</div>
          <div style={{fontSize:"0.65rem",color:"#3a3650"}}>{gallery.length} images</div>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(130px,1fr))",gap:"0.6rem"}}>
          {gallery.map(img=>(
            <div key={img.id} style={{borderRadius:"10px",overflow:"hidden",border:heroImageUrl===img.url?"2px solid #c9a96e":"1px solid #1e1c26",cursor:"pointer",position:"relative",transition:"all 0.15s",aspectRatio:"4/3"}}
              onClick={()=>applyImg(img.url)}
            >
              <img src={img.url} style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
              <div style={{position:"absolute",bottom:0,left:0,right:0,background:"linear-gradient(transparent,rgba(0,0,0,0.7))",padding:"0.2rem 0.4rem",fontSize:"0.6rem",color:"#e8d5b7",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{img.label}</div>
              {heroImageUrl===img.url&&<div style={{position:"absolute",top:"4px",right:"4px",width:"16px",height:"16px",borderRadius:"50%",background:"#c9a96e",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.55rem",fontWeight:700,color:"#0d0b10"}}>✓</div>}
              <button onClick={e=>{e.stopPropagation();removeFromGallery(img.id);}} style={{position:"absolute",top:"4px",left:"4px",width:"20px",height:"20px",borderRadius:"50%",border:"none",background:"rgba(239,68,68,0.85)",color:"#fff",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.6rem",lineHeight:1,opacity:0.7,transition:"opacity 0.15s"}}
                onMouseEnter={e=>e.currentTarget.style.opacity="1"} onMouseLeave={e=>e.currentTarget.style.opacity="0.7"}
              >🗑</button>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function AdminReports({bookings,therapists,services}){
  const today=new Date().toISOString().slice(0,10);
  const woodB=bookings.filter(b=>b.branch==="woodlands");
  const chiliB=bookings.filter(b=>b.branch==="chilanga");
  const bStats=(arr)=>{
    const todayB=arr.filter(b=>b.date===today);
    return{
      total:arr.length,today:todayB.length,
      todayRev:todayB.reduce((s,b)=>s+(b.amount||0),0),
      totalRev:arr.reduce((s,b)=>s+(b.amount||0),0),
      clients:[...new Set(arr.map(b=>b.client))].length,
      paid:arr.filter(b=>b.payment==="paid").reduce((s,b)=>s+(b.amount||0),0)
    };
  };
  const ws=bStats(woodB),cs=bStats(chiliB);
  const topTh=(arr)=>{const m={};arr.forEach(b=>{m[b.therapist]=(m[b.therapist]||0)+1;});const e=Object.entries(m).sort((a,b)=>b[1]-a[1]);return e.length?e[0][0]:"—";};
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%"}}>
      <div style={{marginBottom:"1.3rem"}}>
        <h3 style={{margin:"0 0 0.15rem",fontFamily:"'Cormorant Garamond',serif",fontSize:"1.15rem",color:"#e8d5b7"}}>📈 Branch Performance — {today}</h3>
        <p style={{margin:0,fontSize:"0.7rem",color:"#3a3650"}}>Daily overview comparing Woodlands &amp; Chilanga</p>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"1rem",marginBottom:"1rem"}}>
        {[
          {n:"🌳 Woodlands",b:ws,t:topTh(woodB),col:"#5cdb95"},
          {n:"🌴 Chilanga",b:cs,t:topTh(chiliB),col:"#8b9ef7"},
        ].map(bc=>(
          <div key={bc.n} style={{background:"#0f0d14",border:`1px solid ${bc.col}22`,borderRadius:"14px",padding:"1rem"}}>
            <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.7rem"}}>
              <span style={{fontWeight:600,fontSize:"0.88rem",color:"#e8d5b7"}}>{bc.n}</span>
            </div>
            <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.5rem"}}>
              {[
                {l:"Today Bookings",v:bc.b.today,c:bc.col},
                {l:"Today Revenue",v:`K${bc.b.todayRev.toLocaleString()}`,c:"#c9a96e"},
                {l:"Total Bookings",v:bc.b.total,c:"#8b9ef7"},
                {l:"Total Revenue",v:`K${bc.b.totalRev.toLocaleString()}`,c:"#c9a96e"},
                {l:"Unique Clients",v:bc.b.clients,c:"#a78bfa"},
                {l:"Collected",v:`K${bc.b.paid.toLocaleString()}`,c:"#5cdb95"},
              ].map(s=>(
                <div key={s.l} style={{background:"rgba(255,255,255,0.02)",borderRadius:"8px",padding:"0.5rem"}}>
                  <div style={{fontSize:"1rem",fontWeight:700,color:s.c,fontFamily:"'Cormorant Garamond',serif"}}>{s.v}</div>
                  <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em"}}>{s.l}</div>
                </div>
              ))}
            </div>
            <div style={{marginTop:"0.6rem",fontSize:"0.68rem",color:"#4a4560"}}>Top therapist: <span style={{color:bc.col}}>{bc.t}</span></div>
          </div>
        ))}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1rem"}}>
        <div style={{fontWeight:600,fontSize:"0.82rem",color:"#c8c0b0",marginBottom:"0.8rem"}}>Branch Comparison</div>
        {[
          {l:"Today's Bookings",w:ws.today,c:cs.today},
          {l:"Today Revenue",w:ws.todayRev,c:cs.todayRev,isK:true},
          {l:"Total Bookings",w:ws.total,c:cs.total},
          {l:"Total Revenue",w:ws.totalRev,c:cs.totalRev,isK:true},
          {l:"Unique Clients",w:ws.clients,c:cs.clients},
          {l:"Collected",w:ws.paid,c:cs.paid,isK:true},
        ].map(r=>{
          const max=Math.max(r.w,r.c,1);
          return(
            <div key={r.l} style={{marginBottom:"0.7rem"}}>
              <div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.25rem"}}>
                <span style={{color:"#5a5060"}}>{r.l}</span>
                <span style={{display:"flex",gap:"0.6rem"}}>
                  <span style={{color:"#5cdb95",fontWeight:600}}>🌳 {r.isK?`K${r.w.toLocaleString()}`:r.w}</span>
                  <span style={{color:"#8b9ef7",fontWeight:600}}>🌴 {r.isK?`K${r.c.toLocaleString()}`:r.c}</span>
                </span>
              </div>
              <div style={{display:"flex",gap:"2px",background:"#0d0c13",borderRadius:"4px",height:"8px"}}>
                <div style={{width:`${(r.w/max)*100}%`,height:"100%",background:"#5cdb95",borderRadius:"4px 0 0 4px",transition:"width 0.4s"}}/>
                <div style={{width:`${(r.c/max)*100}%`,height:"100%",background:"#8b9ef7",borderRadius:"0 4px 4px 0",transition:"width 0.4s"}}/>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

function AdminApp({
  bookings = [],
  setBookings = () => {},
  therapists = [],
  setTherapists = () => {},
  services = [],
  setServices = () => {},
  products = [],
  setProducts = () => {},
  notifications = [],
  setNotifications = () => {},
  newBookingsCount = 0,
  heroImageUrl,
  setHeroImageUrl,
  currentAdmin,
  onLogout,
  giftCards = [],
  setGiftCards = () => {},
}) {
  const isSuper = currentAdmin?.role === "superadmin";
  const branch = currentAdmin?.branch;
  const bFilter = (arr,field="branch") => isSuper ? arr : arr.filter(i=>i[field]===branch);
  const [view, setView] = useState("dashboard");
  const [collapsed, setCollapsed] = useState(false);
  const [showNotifs, setShowNotifs] = useState(false);

  // ✅ SAFE FALLBACKS (prevents crashes)
  const safeBookings = Array.isArray(bookings) ? bookings : [];
  const safeNotifications = Array.isArray(notifications) ? notifications : [];

  const unread = safeNotifications.filter(n => !n.read).length;
  const pending = bFilter(safeBookings).filter(b => b.status === "pending").length;

  return (
    <div
      style={{
        height: "100%",
        display: "flex",
        flexDirection: "column",
        background: "#08070f",
        color: "#e8d5b7",
        fontFamily: "'DM Sans',sans-serif"
      }}
    >
      <div style={{ display: "flex", flex: 1, overflow: "visible" }}>
          <Sidebar
            view={view}
            setView={v => {
              setView(v);
              setShowNotifs(false);
            }}
            collapsed={collapsed}
            setCollapsed={setCollapsed}
            pending={pending}
            isSuper={isSuper}
          />

        <div style={{ flex: 1, display: "flex", flexDirection: "column", overflow: "visible" }}>
          <AdminTopBar
            view={view}
            unread={unread}
            onBell={() => setShowNotifs(s => !s)}
            newBookingsCount={newBookingsCount}
            currentAdmin={currentAdmin}
            onLogout={onLogout}
          />

          <div style={{ flex: 1, overflowY: "auto" }}>
            {view === "dashboard" && (
              <AdminDashboard
                bookings={bFilter(safeBookings)}
                notifications={safeNotifications}
                onMarkRead={() =>
                  setNotifications(ns =>
                    (Array.isArray(ns) ? ns : []).map(n => ({ ...n, read: true }))
                  )
                }
                heroImageUrl={heroImageUrl}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "bookings" && (
              <AdminBookings
                bookings={bFilter(safeBookings)}
                setBookings={setBookings}
                therapists={bFilter(therapists)}
                services={bFilter(services)}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "extras" && (
              <AdminExtras
                bookings={bFilter(safeBookings)}
                setBookings={setBookings}
              />
            )}
            {view === "invoices" && (
              <AdminInvoices
                bookings={bFilter(safeBookings)}
                setBookings={setBookings}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "services" && (
              <AdminServices
                services={bFilter(services)}
                setServices={setServices}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "inventory" && (
              <AdminInventory
                products={bFilter(products)}
                setProducts={setProducts}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "giftcards" && (
              <AdminGiftCards giftCards={giftCards} setGiftCards={setGiftCards} currentAdmin={currentAdmin} />
            )}
            {view === "reports" && isSuper && (
              <AdminReports bookings={safeBookings} therapists={therapists} services={services} />
            )}
            {view === "appearance" && (
              <AdminAppearance
                heroImageUrl={heroImageUrl}
                setHeroImageUrl={setHeroImageUrl}
              />
            )}

            {view === "therapists" && (
              <AdminTherapists
                therapists={bFilter(therapists)}
                setTherapists={setTherapists}
                bookings={bFilter(safeBookings)}
                currentAdmin={currentAdmin}
              />
            )}

            {view === "clients" && <AdminClients bookings={bFilter(safeBookings)} currentAdmin={currentAdmin} />}

            {view === "revenue" && <AdminRevenue bookings={bFilter(safeBookings)} currentAdmin={currentAdmin} />}
          </div>
        </div>
      </div>

      {showNotifs && (
        <>
          <NotifPanel
            notifications={safeNotifications}
            setNotifications={setNotifications}
            onClose={() => setShowNotifs(false)}
          />
          <div
            onClick={() => setShowNotifs(false)}
            style={{
              position: "fixed",
              inset: 0,
              zIndex: 140
            }}
          />
        </>
      )}
    </div>
  );
}

// Admin Login Modal
function AdminLoginModal({onLogin,onClose}){
  const [uid,setUid]=useState("");const [pwd,setPwd]=useState("");const [err,setErr]=useState("");
  const handle=()=>{const u=ADMIN_USERS.find(a=>a.id===uid&&a.pass===pwd);if(u){onLogin(u);}else{setErr("Invalid credentials");}};
  return(
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.8)",backdropFilter:"blur(12px)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:800,padding:"1rem"}}>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"18px",padding:"2rem",width:"100%",maxWidth:"380px"}}>
        <div style={{textAlign:"center",marginBottom:"1.5rem"}}>
          <div style={{fontSize:"1.8rem",marginBottom:"0.3rem"}}>🌸</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.2rem",color:"#e8d5b7",fontWeight:600}}>Ngalula Spa Admin</div>
          <div style={{fontSize:"0.7rem",color:"#3a3650",marginTop:"0.2rem"}}>Select your account to continue</div>
        </div>
        <div style={{display:"flex",flexDirection:"column",gap:"0.5rem",marginBottom:"1rem"}}>
          {ADMIN_USERS.map(u=>
            <div key={u.id} onClick={()=>{setUid(u.id);setPwd("");setErr("");}} style={{display:"flex",alignItems:"center",gap:"0.6rem",padding:"0.65rem 0.9rem",borderRadius:"10px",border:`1px solid ${uid===u.id?"#c9a96e":"#1e1c26"}`,background:uid===u.id?"rgba(201,169,110,0.06)":"transparent",cursor:"pointer",transition:"all 0.15s"}}>
              <div style={{width:"32px",height:"32px",borderRadius:"50%",background:uid===u.id?"#c9a96e22":"#0d0c13",border:"1px solid",borderColor:uid===u.id?"#c9a96e44":"#1e1c26",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.75rem",color:uid===u.id?"#c9a96e":"#3a3650"}}>{u.id==="superadmin"?"★":u.branch==="woodlands"?"🌳":"🌴"}</div>
              <div><div style={{fontSize:"0.78rem",color:uid===u.id?"#e8d5b7":"#a89f8c",fontWeight:500}}>{u.name}</div><div style={{fontSize:"0.6rem",color:"#3a3650"}}>{u.label.split("–")[1]?.trim()||""}</div></div>
            </div>
          )}
        </div>
        {uid&&<div style={{marginBottom:"0.8rem"}}>
          <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.3rem"}}>Password</div>
          <input type="password" value={pwd} onChange={e=>setPwd(e.target.value)} onKeyDown={e=>e.key==="Enter"&&handle()} placeholder="Enter password…" style={{width:"100%",padding:"0.6rem 0.8rem",borderRadius:"8px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.82rem",boxSizing:"border-box"}}/>
          {err&&<div style={{fontSize:"0.68rem",color:"#ef4444",marginTop:"0.25rem"}}>✗ {err}</div>}
        </div>}
        <div style={{display:"flex",gap:"0.6rem"}}>
          <GhostBtn onClick={onClose} style={{flex:1,textAlign:"center"}}>Cancel</GhostBtn>
          <PrimaryBtn onClick={handle} disabled={!uid||!pwd} style={{flex:1,textAlign:"center"}}>Sign In</PrimaryBtn>
        </div>
      </div>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  ROOT — SHARED STATE BRIDGE
// ═══════════════════════════════════════════════════════════════════════════
export default function NgalulaUnifiedApp(){
  const [mode,setMode]=useState("client");
  const [bookings,setBookings]=useState(SEED_BOOKINGS.map(b=>({...b,branch:THERAPIST_BRANCH_MAP[b.therapist]||"woodlands"})));
  const [therapists,setTherapists]=useState(SEED_THERAPISTS.map((t,i)=>({...t,branch:i<2?"woodlands":"chilanga"})));
  const [heroImageUrl,setHeroImageUrl]=useState("https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1600&q=80");
  const [notifications,setNotifications]=useState([
    {id:1,type:"booking",  msg:"New booking — Natasha Phiri · Couples' Massage",time:"13:00",read:false},
    {id:2,type:"payment",  msg:"Payment received — Yvonne Musonda · K1,100",    time:"12:30",read:false},
    {id:3,type:"booking",  msg:"New booking — Mutale Kabwe · Deep Cleanse Facial",time:"11:00",read:true},
  ]);
  const [services,setServices]=useState(SERVICES_LIST.map((s,i)=>({...s,branch:i%2===0?"woodlands":"chilanga"})));
  const [products,setProducts]=useState(SEED_PRODUCTS);
  const [newBookingsCount,setNewBookingsCount]=useState(0);
  const [pulseAdmin,setPulseAdmin]=useState(false);
  const [secretClicks,setSecretClicks]=useState(0);
  const [lastSecretClick,setLastSecretClick]=useState(Date.now());
  const [showAdminToast,setShowAdminToast]=useState(false);
  const [toastMessage,setToastMessage]=useState("");
  const [showLogin,setShowLogin]=useState(false);
  const [currentAdmin,setCurrentAdmin]=useState(null);
  const [giftCards,setGiftCards]=useState([]);

  const handleNewBooking=useCallback((booking)=>{
    setBookings(bb=>[booking,...bb]);
    setNotifications(ns=>[{
      id:genId(),
      type:"booking",
      msg:`🟢 LIVE: ${booking.client} booked ${booking.service} — K${booking.amount.toLocaleString()}`,
      time:"Just now",
      read:false
    },...ns]);
    setNewBookingsCount(c=>c+1);
    setPulseAdmin(true);
    setTimeout(()=>setPulseAdmin(false),5000);
  },[]);

  useEffect(()=>{
    if(secretClicks < 1) return;
    setToastMessage(`Hidden access ${secretClicks}/5`);
    setShowAdminToast(true);
    if(secretClicks >= 5){
      setSecretClicks(0);
      if(currentAdmin) setMode(m=>m==="client"?"admin":"client");
      else setShowLogin(true);
    }
    const timer = setTimeout(()=>setShowAdminToast(false),2000);
    return ()=>clearTimeout(timer);
  },[secretClicks,currentAdmin]);

  useEffect(()=>{
    const onKeyDown = (e) => {
      if(e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'a'){
        if(currentAdmin){setMode(m=>m==="client"?"admin":"client");setToastMessage(mode==="client"?"Admin Panel":"Client Booking");setShowAdminToast(true);setTimeout(()=>setShowAdminToast(false),2000);}
        else setShowLogin(true);
      }
    };
    window.addEventListener('keydown', onKeyDown);
    return ()=>window.removeEventListener('keydown', onKeyDown);
  },[currentAdmin,mode]);

  const handleSecretAdminClick=()=>{
    const now = Date.now();
    if(now - lastSecretClick > 4000){
      setSecretClicks(1);
    } else {
      setSecretClicks(c=>c+1);
    }
    setLastSecretClick(now);
  };

  return(
    <div style={{height:"100vh",overflow:"auto",background:"#08070f",fontFamily:"'DM Sans',sans-serif",position:"relative"}}>
      <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
      <div onClick={handleSecretAdminClick} style={{position:"fixed",top:0,right:0,width:"90px",height:"90px",zIndex:700,opacity:0,cursor:"pointer",pointerEvents:"auto"}} title="Hidden admin access" />
        {showAdminToast && (
        <div style={{position:"fixed",top:"18px",right:"18px",background:"rgba(15,13,20,0.95)",border:"1px solid #1e1c26",color:"#e8d5b7",padding:"0.7rem 1rem",borderRadius:"12px",fontSize:"0.8rem",zIndex:750,boxShadow:"0 16px 40px rgba(0,0,0,0.4)"}}>
          {toastMessage}
        </div>
      )}
      {showLogin&&<AdminLoginModal onLogin={(u)=>{setCurrentAdmin(u);setShowLogin(false);setMode("admin");}} onClose={()=>setShowLogin(false)}/>}
      <div style={{height:"100%",paddingTop:"50px",boxSizing:"border-box"}}>
        {mode==="client" && <ClientApp bookings={bookings} therapists={therapists} services={services} onNewBooking={handleNewBooking} heroImageUrl={heroImageUrl} onCreateGiftCard={(gc)=>setGiftCards(gg=>[...gg,gc])} giftCards={giftCards} onRedeemGiftCard={(id)=>setGiftCards(gg=>gg.map(g=>g.id===id?{...g,status:"redeemed",redeemedAt:new Date().toISOString().slice(0,10)}:g))}/>}
        {mode==="admin" && currentAdmin && <AdminApp bookings={bookings} setBookings={setBookings} therapists={therapists} setTherapists={setTherapists} services={services} setServices={setServices} products={products} setProducts={setProducts} notifications={notifications} setNotifications={setNotifications} newBookingsCount={newBookingsCount} heroImageUrl={heroImageUrl} setHeroImageUrl={setHeroImageUrl} currentAdmin={currentAdmin} onLogout={()=>{setCurrentAdmin(null);setMode("client");}} giftCards={giftCards} setGiftCards={setGiftCards}/>}
      </div>
    </div>
  );
}
