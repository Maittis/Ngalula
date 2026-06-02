import React, { useState, useEffect, useMemo, useCallback } from "react";
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Cell
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

const SEED_BOOKINGS = [
  // ── May 1 (Friday) ──
  {id:101,ref:"MAY01-ELZ",client:"Elizabeth",phone:"",email:"",service:"Body Scrub, Massage, Pedicure, Gel Manicure, Vitamin C Facial",cat:"Body Treatments",therapist:"Grace",date:"2026-05-01",time:"09:00",amount:1635,status:"completed",payment:"cash",note:"Body scrub & massage on gift card; pedicure, removal, gel manicure & vitamin c facial. (GS, AM)",source:"existing"},
  {id:102,ref:"MAY01-PKD",client:"Precious Kasakula & Dad",phone:"",email:"",service:"Men's Pedicure & Manicure, Gel Manicure & Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-01",time:"11:00",amount:1750,status:"completed",payment:"airtel",note:"(GS, AM)",source:"existing"},
  {id:103,ref:"MAY01-MM",client:"Mrs Precious Mweele Mannda",phone:"",email:"",service:"Ukuchina Massage",cat:"Massage",therapist:"Memory",date:"2026-05-01",time:"14:00",amount:950,status:"completed",payment:"airtel",note:"(MM)",source:"existing"},
  // ── May 2 (Saturday) ──
  {id:104,ref:"MAY02-DJ",client:"Djenabou",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-02",time:"10:00",amount:250,status:"completed",payment:"cash",note:"(GS)",source:"existing"},
  // ── May 3 (Sunday) ──
  {id:105,ref:"MAY03-MK",client:"Mulemwa Kusemwa",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-03",time:"09:00",amount:300,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  {id:106,ref:"MAY03-MF",client:"Muwezwa Francinah",phone:"",email:"",service:"Pedicure, Body Scrub, Deep Cleanse Facial",cat:"Body Treatments",therapist:"Aisha",date:"2026-05-03",time:"10:30",amount:1612,status:"completed",payment:"airtel_cash",note:"HC Makeni — Airtel money & cash (AM, GS)",source:"existing"},
  {id:107,ref:"MAY03-TM",client:"Tumbikani Museteka",phone:"",email:"",service:"Body Scrub, Deep Tissue Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-03",time:"13:00",amount:1400,status:"completed",payment:"airtel",note:"(GS, MM)",source:"existing"},
  // ── May 7 (Thursday) ──
  {id:108,ref:"MAY07-KR",client:"Karin",phone:"",email:"",service:"Deep Cleanse Facial",cat:"Facials",therapist:"Aisha",date:"2026-05-07",time:"10:00",amount:612,status:"completed",payment:"cash",note:"Complimentary leg massage. (AM, GS)",source:"existing"},
  // ── May 8 (Friday) ──
  {id:109,ref:"MAY08-FM",client:"Faith Mpondela",phone:"",email:"",service:"Signature Massage",cat:"Massage",therapist:"Memory",date:"2026-05-08",time:"09:00",amount:800,status:"completed",payment:"airtel_p2cell",note:"K400 Airtel, K400 P2cell (MM)",source:"existing"},
  {id:110,ref:"MAY08-BN",client:"Binta",phone:"",email:"",service:"Body Scrub, Ngalula Recovery Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-08",time:"11:00",amount:2000,status:"completed",payment:"airtel",note:"Body scrub K750 (special price), recovery massage. (MM)",source:"existing"},
  {id:111,ref:"MAY08-RS",client:"Regina Sakala",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Memory",date:"2026-05-08",time:"14:00",amount:750,status:"completed",payment:"cash",note:"K750 special price (closed & opened for her). (MM)",source:"existing"},
  // ── May 9 (Saturday) ──
  {id:112,ref:"MAY09-JM",client:"Judy Mumba",phone:"",email:"",service:"Pedicure, Gel Removal, Gel Paint",cat:"Nails",therapist:"Grace",date:"2026-05-09",time:"09:00",amount:600,status:"completed",payment:"partial",note:"Paid K150 towards next treatment. (GS)",source:"existing"},
  {id:113,ref:"MAY09-TM",client:"Teclah Munanku",phone:"",email:"",service:"Pedicure, Pregnancy Massage",cat:"Nails",therapist:"Memory",date:"2026-05-09",time:"11:00",amount:1550,status:"completed",payment:"airtel",note:"(GS, MM)",source:"existing"},
  {id:114,ref:"MAY09-NN",client:"Anonymous Man",phone:"",email:"",service:"Massage",cat:"Massage",therapist:"Grace",date:"2026-05-09",time:"14:00",amount:800,status:"completed",payment:"cash",note:"Wanted Grace to go have sex with him after massage. K800 cash.",source:"existing"},
  // ── May 10 (Sunday) ──
  {id:115,ref:"MAY10-BL",client:"Bertha Lishomwa",phone:"",email:"",service:"Body Scrub, Deep Tissue Massage",cat:"Body Treatments",therapist:"Aisha",date:"2026-05-10",time:"09:00",amount:1400,status:"completed",payment:"airtel",note:"(GS, AM)",source:"existing"},
  {id:116,ref:"MAY10-SK",client:"Shammah Kalala",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Grace",date:"2026-05-10",time:"12:00",amount:550,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 11 (Monday) ──
  {id:117,ref:"MAY11-TC",client:"Tendai Chaiwila",phone:"",email:"",service:"Body Scrub, Swedish Massage",cat:"Body Treatments",therapist:"Grace",date:"2026-05-11",time:"09:00",amount:1100,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  {id:118,ref:"MAY11-CK",client:"Chola Kaunda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-11",time:"11:30",amount:500,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 13 (Wednesday) ──
  {id:119,ref:"MAY13-NN",client:"Neno",phone:"",email:"",service:"Pedicure, Gel Removal, Body Scrub",cat:"Nails",therapist:"Grace",date:"2026-05-13",time:"10:00",amount:1100,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 14 (Thursday) ──
  {id:120,ref:"MAY14-TN",client:"Tamara Ngoma",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-14",time:"09:00",amount:450,status:"completed",payment:"ewallet",note:"(GS)",source:"existing"},
  {id:121,ref:"MAY14-RK",client:"Hon Roselyn Kiwala",phone:"",email:"",service:"Pedicure, Lashes",cat:"Nails",therapist:"Grace",date:"2026-05-14",time:"10:30",amount:950,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 16 (Saturday) ──
  {id:122,ref:"MAY16-KS",client:"Karen Simonde",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-16",time:"08:00",amount:450,status:"completed",payment:"cash",note:"Added K50 for coming in abruptly. (GS)",source:"existing"},
  {id:123,ref:"MAY16-CM",client:"Clara Musonda",phone:"",email:"",service:"Pedicure with Stickons",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"09:00",amount:575,status:"completed",payment:"airtel",note:"Stickons on big toes + removal. (NC)",source:"existing"},
  {id:124,ref:"MAY16-PS",client:"Pamela Sikana",phone:"",email:"",service:"Pedicure with Stickons, Lashes",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"10:00",amount:1450,status:"completed",payment:"airtel",note:"Pedicure with stickons K550 + lashes K900. (NC)",source:"existing"},
  {id:125,ref:"MAY16-SC",client:"Simon Chitanda",phone:"",email:"",service:"Signature Massage",cat:"Massage",therapist:"Memory",date:"2026-05-16",time:"11:00",amount:950,status:"completed",payment:"airtel",note:"Focus on back and head — added K50 tip. (MM)",source:"existing"},
  {id:126,ref:"MAY16-JM",client:"Jones Mpakateni",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Grace",date:"2026-05-16",time:"12:00",amount:550,status:"completed",payment:"cash",note:"(GS)",source:"existing"},
  {id:127,ref:"MAY16-ROS",client:"Rose (Hon Milner Mwanakampwe)",phone:"",email:"",service:"Nails, Body Scrub, Massage",cat:"Nails",therapist:"Grace",date:"2026-05-16",time:"13:00",amount:1350,status:"completed",payment:"paid",note:"Nails K550 + body scrub + massage K1,350. (GS, NC, MM)",source:"existing"},
  {id:128,ref:"MAY16-PM",client:"Petronella Mulenga & Husband",phone:"",email:"",service:"Couples' Pedicure & Manicure",cat:"Nails",therapist:"Natasha Chanda",date:"2026-05-16",time:"15:00",amount:1350,status:"completed",payment:"cash",note:"Couples K1,100 + daughter's pedicure K250 (K100 discount). All gel paint & nails done by Natasha Chanda. Natasha got K800.",source:"existing"},
  // ── May 17 (Sunday) ──
  {id:129,ref:"MAY17-AC",client:"Angela Chisembele",phone:"",email:"",service:"Body Scrub, Vitamin C Facial, Mini Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-17",time:"09:00",amount:1950,status:"completed",payment:"airtel",note:"Paid K200 reservation fee. (GS, MM)",source:"existing"},
  {id:130,ref:"MAY17-KH",client:"Kahilu",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-17",time:"11:00",amount:350,status:"completed",payment:"airtel_cash",note:"K200 Airtel, K150 cash. (GS)",source:"existing"},
  {id:131,ref:"MAY17-SB",client:"Serge Bapaga",phone:"",email:"",service:"Body Scrub, Ngalula Rejuvenation Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-17",time:"12:00",amount:2100,status:"completed",payment:"cash",note:"(GS, MM)",source:"existing"},
  {id:132,ref:"MAY17-MK",client:"Mwaba Kaunda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-17",time:"15:00",amount:350,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 18 (Monday) ──
  {id:133,ref:"MAY18-MN",client:"Mirriam Nyirenda",phone:"",email:"",service:"Pregnancy Massage",cat:"Massage",therapist:"Memory",date:"2026-05-18",time:"10:00",amount:1100,status:"completed",payment:"airtel",note:"6-7 months. Paid for by husband. (MM). K1,100 invested in Patumba.",source:"existing"},
  // ── May 19 (Tuesday) ──
  {id:134,ref:"MAY19-SK",client:"Sara Kalende",phone:"",email:"",service:"Body Scrub",cat:"Body Treatments",therapist:"Memory",date:"2026-05-19",time:"10:00",amount:550,status:"completed",payment:"airtel",note:"Closed — personal use. (MM)",source:"existing"},
  // ── May 21 (Thursday) ──
  {id:135,ref:"MAY21-VM",client:"Victor Mungole",phone:"",email:"",service:"Deep Tissue Massage",cat:"Massage",therapist:"Grace",date:"2026-05-21",time:"09:00",amount:850,status:"completed",payment:"cash",note:"(GS)",source:"existing"},
  {id:136,ref:"MAY21-SS",client:"Stanslous Shabbuwa (Wife)",phone:"",email:"",service:"Body Scrub, Massage, Pedicure",cat:"Body Treatments",therapist:"Grace",date:"2026-05-21",time:"10:30",amount:2350,status:"completed",payment:"airtel",note:"Paid K2,350 but wife didn't come (travelling) — pedicure gift card. (GS)",source:"existing"},
  // ── May 22 (Friday) ──
  {id:137,ref:"MAY22-FB",client:"Faith Banda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-22",time:"09:00",amount:350,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  {id:138,ref:"MAY22-MJS",client:"Mmaphuti Jackina Sindimba",phone:"",email:"",service:"Dermaplaning with Vitamin C Facial, Deep Cleanse Facial",cat:"Facials",therapist:"Memory",date:"2026-05-22",time:"10:00",amount:1462,status:"completed",payment:"airtel",note:"Dermaplaning w/ vitamin C K850 + deep cleanse K612 (mother & daughter). (MM)",source:"existing"},
  // ── May 23 (Saturday) ──
  {id:139,ref:"MAY23-AM",client:"Audrey Mwape",phone:"",email:"",service:"Pedicure with Removal",cat:"Nails",therapist:"Grace",date:"2026-05-23",time:"09:00",amount:500,status:"completed",payment:"airtel",note:"Didn't want to pay for removal — left bad review on Facebook. (GS)",source:"existing"},
  {id:140,ref:"MAY23-PK",client:"Patson Kaluwaya (Ba Mwisho)",phone:"",email:"",service:"Body Scrub, Deep Cleanse Facial, Mini Back Massage",cat:"Body Treatments",therapist:"Memory",date:"2026-05-23",time:"10:00",amount:2124,status:"completed",payment:"cash",note:"Wife's body scrub K550, wife's facial K612, his facial K612, his mini back massage K350. Enjoyed — wants to buy scrubs & cream for wife. (MM, GS)",source:"existing"},
  {id:141,ref:"MAY23-LS",client:"Liswaniso",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-23",time:"14:00",amount:350,status:"completed",payment:"cash",note:"Napsa flats. (GS)",source:"existing"},
  // ── May 24 (Sunday) ──
  {id:142,ref:"MAY24-CN",client:"Chileshe V Nkole",phone:"",email:"",service:"Lashes, Dermaplaning with Vitamin C Facial",cat:"Facials",therapist:"Grace",date:"2026-05-24",time:"09:00",amount:1200,status:"completed",payment:"fnb_pay2cell",note:"Lashes K350 + dermaplaning w/ vitamin C K850 (complimentary palm massage). (GS, MM)",source:"existing"},
  {id:143,ref:"MAY24-SS",client:"Stephen Simasiku & Partner",phone:"",email:"",service:"Couples' Body Scrub + Ngalula Custom Unwind",cat:"Massage",therapist:"Memory",date:"2026-05-24",time:"11:00",amount:5500,status:"completed",payment:"airtel",note:"(GS, MM)",source:"existing"},
  // ── May 25 (Monday) ──
  {id:144,ref:"MAY25-YM",client:"Yumba Muleba",phone:"",email:"",service:"Pedicure with Removal",cat:"Nails",therapist:"Grace",date:"2026-05-25",time:"09:00",amount:500,status:"completed",payment:"cash",note:"Closed at 12 due to holiday & fatigue. (GS)",source:"existing"},
  // ── May 26 (Tuesday) ──
  {id:145,ref:"MAY26-CV",client:"Chimuka Victor",phone:"",email:"",service:"Body Scrub, Relaxation Massage",cat:"Body Treatments",therapist:"Grace",date:"2026-05-26",time:"09:00",amount:1800,status:"completed",payment:"cash_airtel",note:"K1,600 cash + K200 Airtel. (GS)",source:"existing"},
  {id:146,ref:"MAY26-FT",client:"Fatima",phone:"",email:"",service:"Ngalula Rejuvenation Massage",cat:"Massage",therapist:"Memory",date:"2026-05-26",time:"11:30",amount:1550,status:"completed",payment:"airtel",note:"K100 late fee included. (MM)",source:"existing"},
  // ── May 27 (Wednesday) ──
  {id:147,ref:"MAY27-JM",client:"Janet Mundando",phone:"",email:"",service:"Dermaplaning with Vitamin C Facial, Pedicure, Body Scrub",cat:"Facials",therapist:"Memory",date:"2026-05-27",time:"09:00",amount:1512,status:"completed",payment:"airtel",note:"Dermaplaning w/ vitamin C (charged deep cleanse) K612, pedicure K450, body scrub K550, K100 discount. (MM, GS)",source:"existing"},
  {id:148,ref:"MAY27-MN",client:"Mwandu Nachangwa",phone:"",email:"",service:"Extraction with Vitamin C Facial, Pedicure, Lashes",cat:"Facials",therapist:"Grace",date:"2026-05-27",time:"11:30",amount:1700,status:"completed",payment:"airtel",note:"Extraction w/ vitamin C K850, pedicure + removal K500, lashes K350. K238 balance to be paid when lashes done. (MM, GS)",source:"existing"},
  // ── May 28 (Thursday) ──
  {id:149,ref:"MAY28-NM",client:"Ngosa Masuzyo",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-28",time:"09:00",amount:500,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  {id:150,ref:"MAY28-PS2",client:"Pamela Sikana",phone:"",email:"",service:"Lash Refill",cat:"Lashes",therapist:"Grace",date:"2026-05-28",time:"10:30",amount:150,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  // ── May 29 (Friday) ──
  {id:151,ref:"MAY29-CV2",client:"Chimuka Victor",phone:"",email:"",service:"Pedicure",cat:"Nails",therapist:"Grace",date:"2026-05-29",time:"10:00",amount:525,status:"completed",payment:"cash",note:"(GS)",source:"existing"},
  // ── May 31 (Sunday) ──
  {id:152,ref:"MAY31-KM",client:"Ketty Musukwa",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-31",time:"09:00",amount:550,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
  {id:153,ref:"MAY31-NK",client:"Natasha Kabanda",phone:"",email:"",service:"Lashes",cat:"Lashes",therapist:"Grace",date:"2026-05-31",time:"10:30",amount:350,status:"completed",payment:"airtel",note:"(GS)",source:"existing"},
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
const COLORS_PRESET = ["#c9a96e","#d4a8ff","#5cdb95","#ff9eb5","#8b9ef7","#ffb347","#5bc8f5","#f97d6e"];
const AIRTEL_NUMBER = "0971 234 567";

const genRef = () => "NGS-" + Math.random().toString(36).slice(2,8).toUpperCase();
const genId  = () => Date.now() + Math.floor(Math.random()*9999);
const fmtDate = d => `${DAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]}`;
const getDates = () => { const out=[]; const d=new Date(); d.setDate(d.getDate()+1); while(out.length<21){if(d.getDay()!==0)out.push(new Date(d));d.setDate(d.getDate()+1);} return out; };

// ─── SHARED TINY COMPONENTS ───────────────────────────────────────────────
const SBadge = ({s}) => { const m=STATUS_META[s]||STATUS_META.pending; return <span style={{padding:"0.17rem 0.55rem",borderRadius:"20px",fontSize:"0.64rem",fontWeight:600,background:m.bg,color:m.c,whiteSpace:"nowrap"}}>{m.l}</span>; };
const PBadge = ({s}) => { const m=PAY_META[s]||PAY_META.unpaid; return <span style={{fontSize:"0.7rem",color:m.c,fontWeight:600}}>{m.l}</span>; };

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
      <style>{`@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}} @keyframes spin{to{transform:rotate(360deg)}} @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}} @keyframes slideIn{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}`}</style>
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

function HeroSection({onBook,heroImageUrl}){
  return(
    <div style={{minHeight:"100vh",display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",position:"relative",overflow:"hidden",textAlign:"center",padding:"6rem 1.5rem 2rem"}}>
      <div style={{position:"absolute",inset:0,backgroundColor:"#08070f",backgroundImage:heroImageUrl?`url(${heroImageUrl})`:"none",backgroundSize:"cover",backgroundPosition:"center",backgroundRepeat:"no-repeat",filter:"brightness(0.45) saturate(1.05)"}}/>
      <div style={{position:"absolute",inset:0,background:"linear-gradient(180deg,rgba(8,7,15,0.65) 0%,rgba(8,7,15,0.9) 100%)"}}/>
      <div style={{position:"absolute",inset:0,background:"radial-gradient(ellipse 80% 50% at 50% 30%,rgba(201,169,110,0.06),transparent 65%)"}}/>
      <div style={{position:"absolute",inset:0,background:"radial-gradient(ellipse 40% 60% at 15% 85%,rgba(92,219,149,0.03),transparent 55%)"}}/>
      {[600,400,220].map((s,i)=><div key={s} style={{position:"absolute",width:`${s}px`,height:`${s}px`,borderRadius:"50%",border:`1px solid rgba(201,169,110,${0.04+i*0.02})`,top:"50%",left:"50%",transform:"translate(-50%,-50%)",pointerEvents:"none"}}/>)}
      <div style={{position:"relative",zIndex:2,maxWidth:"520px",animation:"fadeIn 0.7s ease both"}}>
        <div style={{marginBottom:"1.5rem",fontSize:"2.8rem",filter:"drop-shadow(0 0 20px rgba(201,169,110,0.3))"}}>🌸</div>
        <p style={{fontFamily:"'Cormorant Garamond',serif",fontStyle:"italic",fontSize:"0.82rem",color:"#6e6460",letterSpacing:"0.25em",textTransform:"uppercase",margin:"0 0 0.4rem"}}>Welcome to</p>
        <h1 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"clamp(2.8rem,7vw,5rem)",fontWeight:600,color:"#e8d5b7",margin:"0 0 0.4rem",lineHeight:1.05,letterSpacing:"0.03em"}}>Ngalula Spa</h1>
        <div style={{width:"60px",height:"1px",background:"linear-gradient(90deg,transparent,#c9a96e,transparent)",margin:"1rem auto"}}/>
        <p style={{fontSize:"0.95rem",color:"#7a7068",lineHeight:1.8,margin:"0 auto 2.5rem",fontWeight:300}}>Indulge in transformative wellness experiences crafted for your body, mind &amp; spirit.</p>
        <PrimaryBtn onClick={onBook} style={{padding:"1rem 3rem",fontSize:"1rem",borderRadius:"12px",boxShadow:"0 10px 40px rgba(201,169,110,0.2)"}}>Book Your Session ✦</PrimaryBtn>
        <div style={{display:"flex",gap:"0.5rem",justifyContent:"center",marginTop:"2.5rem",flexWrap:"wrap"}}>
          {["🫧 Massage","✨ Facials","💅 Nails","🌿 Body Treatments"].map(f=><span key={f} style={{padding:"0.3rem 0.85rem",borderRadius:"20px",border:"1px solid rgba(201,169,110,0.15)",color:"#4a4560",fontSize:"0.7rem"}}>{f}</span>)}
        </div>
      </div>
    </div>
  );
}

function ServiceSelector({cart,toggle,total,onNext}){
  const [catF,setCatF]=useState("All");
  const cats=Object.keys(CAT_META);
  const filtered=catF==="All"?SERVICES_LIST:SERVICES_LIST.filter(s=>s.cat===catF);
  return(
    <div style={{maxWidth:"740px",margin:"0 auto",padding:"1.8rem 1.5rem 7rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Your Services</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Select one or more — your total updates live.</p>
      <div style={{display:"flex",gap:"0.4rem",marginBottom:"1.2rem",flexWrap:"wrap"}}>
        {cats.map(c=>{const m=CAT_META[c],act=catF===c;return(<button key={c} onClick={()=>setCatF(c)} style={{padding:"0.28rem 0.85rem",borderRadius:"20px",border:`1px solid ${act?m.col:"#2a2633"}`,background:act?m.bg:"transparent",color:act?m.col:"#4a4560",cursor:"pointer",fontSize:"0.74rem",fontFamily:"'DM Sans',sans-serif"}}>{m.icon} {c}</button>);})}
      </div>
      <div style={{display:"flex",flexDirection:"column",gap:"0.4rem"}}>
        {filtered.map(svc=>{
          const inCart=cart.some(i=>i.name===svc.name),m=CAT_META[svc.cat];
          return(
            <div key={svc.name} onClick={()=>toggle(svc)} style={{display:"flex",justifyContent:"space-between",alignItems:"center",padding:"0.85rem 1rem",background:inCart?m.bg:"#13111a",border:`1px solid ${inCart?m.col+"55":"#1e1c26"}`,borderRadius:"11px",cursor:"pointer",transition:"all 0.15s",gap:"0.8rem"}}>
              <div style={{flex:1,minWidth:0}}>
                <div style={{fontWeight:500,color:inCart?"#e8d5b7":"#a89f8c",fontSize:"0.86rem",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{svc.name}</div>
                <div style={{fontSize:"0.67rem",color:"#4a4560",marginTop:"0.15rem"}}>{m.icon} {svc.cat}</div>
              </div>
              <div style={{display:"flex",alignItems:"center",gap:"0.7rem",flexShrink:0}}>
                <span style={{fontWeight:700,color:m.col,fontSize:"0.86rem"}}>K{svc.price.toLocaleString()}</span>
                <div style={{width:"22px",height:"22px",borderRadius:"6px",border:`2px solid ${inCart?m.col:"#2a2633"}`,background:inCart?m.col:"transparent",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.78rem",color:inCart?"#0d0b10":"#3a3650",fontWeight:700}}>{inCart?"✓":"+"}</div>
              </div>
            </div>
          );
        })}
      </div>
      <div style={{position:"fixed",bottom:0,left:0,right:0,background:"rgba(13,11,16,0.95)",backdropFilter:"blur(16px)",borderTop:"1px solid #16141f",padding:"1rem 1.5rem",display:"flex",alignItems:"center",justifyContent:"space-between",zIndex:50,gap:"1rem"}}>
        <div>
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

function ReviewStep({cart,total,selDate,selTime,therapist,onBack,onNext}){
  return(
    <div style={{maxWidth:"560px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Review Your Booking</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Confirm everything looks right before paying.</p>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.2rem",marginBottom:"0.9rem"}}>
        <div style={{fontSize:"0.63rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.9rem"}}>Selected Services</div>
        {cart.map(s=><div key={s.name} style={{display:"flex",justifyContent:"space-between",fontSize:"0.83rem",padding:"0.3rem 0",borderBottom:"1px solid #16141f"}}><span style={{color:"#a89f8c",flex:1,minWidth:0,overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap",paddingRight:"0.5rem"}}>{s.name}</span><span style={{color:"#c9a96e",fontWeight:600,flexShrink:0}}>K{s.price.toLocaleString()}</span></div>)}
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

function ConfirmationStep({bookingRef,total,therapist,selDate,selTime,cart,onSwitchAdmin}){
  const [copied,setCopied]=useState(false);
  const copy=()=>{ try{navigator.clipboard.writeText(bookingRef);}catch(_){} setCopied(true); setTimeout(()=>setCopied(false),2000); };
  return(
    <div style={{maxWidth:"500px",margin:"0 auto",padding:"2rem 1.5rem",animation:"fadeIn 0.5s ease both"}}>
      <div style={{textAlign:"center",marginBottom:"1.8rem"}}>
        <div style={{width:"66px",height:"66px",borderRadius:"50%",background:"rgba(92,219,149,0.12)",border:"2px solid #5cdb9566",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"1.8rem",margin:"0 auto 1rem",boxShadow:"0 0 30px rgba(92,219,149,0.1)"}}>✓</div>
        <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.8rem",color:"#5cdb95",margin:"0 0 0.3rem"}}>Booking Confirmed!</h2>
        <p style={{color:"#4a4560",fontSize:"0.8rem",margin:0}}>Your slot is reserved. Admin has been notified. Complete Airtel Money payment to finalise.</p>
      </div>
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

function ClientApp({bookings,therapists,onNewBooking,onSwitchAdmin,heroImageUrl}){
  const [step,setStep]=useState(0);
  const [cart,setCart]=useState([]);
  const [selDate,setSelDate]=useState(null);
  const [selTime,setSelTime]=useState(null);
  const [therapist,setTherapist]=useState(null);
  const [bookingRef,setBookingRef]=useState("");
  const dates=useMemo(()=>getDates(),[]);
  const total=cart.reduce((s,i)=>s+i.price,0);

  const toggleSvc=(svc)=>setCart(c=>c.some(i=>i.name===svc.name)?c.filter(i=>i.name!==svc.name):[...c,svc]);

  const handleAuth=(userData)=>{
    const ref=genRef();
    const dateStr=selDate?selDate.toISOString().slice(0,10):"";
    const mainSvc=cart[0];
    const newBooking={
      id:genId(), ref, source:"client",
      client:userData.name||userData.email.split("@")[0],
      phone:userData.phone, email:userData.email,
      service:mainSvc?.name||"", cat:mainSvc?.cat||"",
      therapist:therapist?.name||"", date:dateStr, time:selTime||"",
      amount:total, status:"pending", payment:"unpaid",
      note:`${cart.length>1?`+${cart.length-1} more service(s). `:""}Booked via app.`,
    };
    setBookingRef(ref);
    onNewBooking(newBooking);
    setStep(6);
  };

  return(
    <div style={{minHeight:"100vh",background:"#08070f",color:"#e8d5b7",fontFamily:"'DM Sans',sans-serif",paddingTop: step>0&&step<6?"100px":"50px"}}>
      {step>0&&step<6&&<div style={{position:"fixed",top:"50px",left:0,right:0,zIndex:50,background:"rgba(8,7,15,0.92)",backdropFilter:"blur(16px)"}}><ProgressBar step={step}/></div>}
      {step===0&&<HeroSection onBook={()=>setStep(1)} heroImageUrl={heroImageUrl}/>}
      {step===1&&<ServiceSelector cart={cart} toggle={toggleSvc} total={total} onNext={()=>setStep(2)}/>}
      {step===2&&<DateTimePicker dates={dates} selDate={selDate} setSelDate={setSelDate} selTime={selTime} setSelTime={setSelTime} bookings={bookings} therapistName={therapist?.name} onBack={()=>setStep(1)} onNext={()=>setStep(3)}/>}
      {step===3&&<TherapistPicker therapists={therapists} bookings={bookings} selDate={selDate} selTime={selTime} selected={therapist} setSelected={setTherapist} onBack={()=>setStep(2)} onNext={()=>setStep(4)}/>}
      {step===4&&<ReviewStep cart={cart} total={total} selDate={selDate} selTime={selTime} therapist={therapist} onBack={()=>setStep(3)} onNext={()=>setStep(5)}/>}
      {step===5&&<AuthStep total={total} onSubmit={handleAuth} onBack={()=>setStep(4)}/>}
      {step===6&&<ConfirmationStep bookingRef={bookingRef} total={total} therapist={therapist} selDate={selDate} selTime={selTime} cart={cart} onSwitchAdmin={onSwitchAdmin}/>}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  ADMIN PANEL
// ═══════════════════════════════════════════════════════════════════════════

const ADMIN_NAV=[{id:"dashboard",icon:"◈",label:"Dashboard"},{id:"bookings",icon:"📋",label:"Bookings"},{id:"therapists",icon:"👥",label:"Therapists"},{id:"clients",icon:"🧑‍🤝‍🧑",label:"Clients"},{id:"revenue",icon:"📊",label:"Revenue"}];

function Sidebar({view,setView,collapsed,setCollapsed,pending}){
  return(
    <div style={{width:collapsed?"54px":"206px",background:"#0b0a10",borderRight:"1px solid #16141f",display:"flex",flexDirection:"column",transition:"width 0.22s ease",flexShrink:0,zIndex:10}}>
      <div style={{padding:collapsed?"1rem 0":"1.1rem",borderBottom:"1px solid #16141f",display:"flex",alignItems:"center",gap:"0.6rem",overflow:"hidden"}}>
        <span style={{fontSize:"1.2rem",flexShrink:0,display:"block",textAlign:"center",width:collapsed?"100%":"auto"}}>🌸</span>
        {!collapsed&&<div><div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",fontWeight:600,whiteSpace:"nowrap",letterSpacing:"0.02em"}}>Ngalula Spa</div><div style={{fontSize:"0.54rem",color:"#3a3650",letterSpacing:"0.14em",textTransform:"uppercase"}}>Admin Panel</div></div>}
      </div>
      <nav style={{flex:1,padding:"0.5rem 0"}}>
        {ADMIN_NAV.map(item=>{
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

function AdminTopBar({view,unread,onBell,newBookingsCount}){
  const T={dashboard:"Dashboard",bookings:"Bookings",therapists:"Therapists",clients:"Clients",revenue:"Revenue & Analytics"};
  return(
    <div style={{height:"50px",background:"#0b0a10",borderBottom:"1px solid #16141f",display:"flex",alignItems:"center",justifyContent:"space-between",padding:"0 1.3rem",flexShrink:0}}>
      <div style={{display:"flex",alignItems:"center",gap:"0.8rem"}}>
        <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7",fontWeight:500}}>{T[view]}</span>
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
        <div style={{display:"flex",alignItems:"center",gap:"0.45rem"}}>
          <div style={{width:"28px",height:"28px",borderRadius:"50%",background:"rgba(201,169,110,0.1)",border:"1px solid rgba(201,169,110,0.2)",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.6rem",color:"#c9a96e",fontWeight:700}}>AD</div>
          <span style={{fontSize:"0.74rem",color:"#4a4560"}}>Admin</span>
        </div>
      </div>
    </div>
  );
}

// Booking Form
function BookingFormModal({booking,therapists,onSave,onClose}){
  const first=SERVICES_LIST[0];
  const empty={client:"",phone:"",service:first.name,cat:first.cat,therapist:therapists[0]?.name||"",date:"",time:"09:00",amount:first.price,status:"pending",payment:"unpaid",note:""};
  const [form,setForm]=useState(booking?{client:booking.client,phone:booking.phone,service:booking.service,cat:booking.cat,therapist:booking.therapist,date:booking.date,time:booking.time,amount:booking.amount,status:booking.status,payment:booking.payment,note:booking.note||""}:empty);
  const [errors,setErrors]=useState({});
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));
  const pickSvc=(n)=>{const s=SERVICES_LIST.find(x=>x.name===n);if(s)setForm(f=>({...f,service:s.name,cat:s.cat,amount:s.price}));};
  const validate=()=>{const e={};if(!form.client.trim())e.client="Required";if(!form.phone.trim())e.phone="Required";if(!form.date)e.date="Required";if(!form.amount||isNaN(+form.amount)||+form.amount<=0)e.amount="Required";setErrors(e);return!Object.keys(e).length;};
  const R2={display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem"};
  return(
    <BaseModal title={booking?`Edit — ${booking.ref}`:"New Booking"} subtitle={booking?"Update booking details":"Create a new booking"} onClose={onClose} wide>
      <div style={R2}>
        <FField label="Client Name" error={errors.client}><input style={{...SI,borderColor:errors.client?"#ef444455":"#1e1c26"}} value={form.client} onChange={e=>upd("client",e.target.value)} placeholder="Full name"/></FField>
        <FField label="Phone" error={errors.phone}><input style={{...SI,borderColor:errors.phone?"#ef444455":"#1e1c26"}} value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="+260 97 XXX"/></FField>
      </div>
      <FField label="Service"><select style={{...SI,cursor:"pointer"}} value={form.service} onChange={e=>pickSvc(e.target.value)}>{SERVICES_LIST.map(s=><option key={s.name} value={s.name}>{s.name} — K{s.price.toLocaleString()}</option>)}</select></FField>
      <div style={R2}>
        <FField label="Therapist"><select style={{...SI,cursor:"pointer"}} value={form.therapist} onChange={e=>upd("therapist",e.target.value)}>{therapists.filter(t=>t.active).map(t=><option key={t.id} value={t.name}>{t.name}</option>)}</select></FField>
        <FField label="Amount (K)" error={errors.amount}><input style={{...SI,borderColor:errors.amount?"#ef444455":"#1e1c26"}} type="number" min="0" value={form.amount} onChange={e=>upd("amount",e.target.value)}/></FField>
      </div>
      <div style={R2}>
        <FField label="Date" error={errors.date}><input style={{...SI,borderColor:errors.date?"#ef444455":"#1e1c26"}} type="date" value={form.date} onChange={e=>upd("date",e.target.value)}/></FField>
        <FField label="Time"><select style={{...SI,cursor:"pointer"}} value={form.time} onChange={e=>upd("time",e.target.value)}>{TIME_SLOTS.map(t=><option key={t} value={t}>{t}</option>)}</select></FField>
      </div>
      <div style={R2}>
        <FField label="Status"><select style={{...SI,cursor:"pointer"}} value={form.status} onChange={e=>upd("status",e.target.value)}>{Object.entries(STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
        <FField label="Payment"><select style={{...SI,cursor:"pointer"}} value={form.payment} onChange={e=>upd("payment",e.target.value)}>{Object.entries(PAY_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
      </div>
      <FField label="Admin Notes"><textarea style={{...SI,resize:"vertical",minHeight:"58px",padding:"0.5rem 0.9rem"}} value={form.note} onChange={e=>upd("note",e.target.value)} placeholder="Special instructions…"/></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.4rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave({...form,amount:+form.amount});}} style={{flex:2}}>{booking?"Save Changes":"Create Booking"}</PrimaryBtn></div>
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
  heroImageUrl,
  setHeroImageUrl
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

  const [heroUrlInput, setHeroUrlInput] = useState(heroImageUrl || "");

  const updateHeroUrl = () => {
    if (setHeroImageUrl) setHeroImageUrl(heroUrlInput.trim());
  };

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

      {/* HERO CONTROL */}
      <div style={{
        background: "#0f0d14",
        border: "1px solid #1e1c26",
        borderRadius: "14px",
        padding: "1rem",
        display: "grid",
        gridTemplateColumns: "1fr 280px",
        gap: "1rem",
        marginBottom: "1.2rem"
      }}>
        <div>
          <div style={{ fontWeight: 600, marginBottom: "0.4rem", fontSize:"0.85rem" }}>
            Hero Banner Control
          </div>
          <div style={{ fontSize: "0.75rem", color: "#5a5060", marginBottom: "0.8rem" }}>
            Update the booking page banner in real-time.
          </div>
          <div style={{ display: "flex", gap: "0.6rem" }}>
            <input value={heroUrlInput} onChange={(e) => setHeroUrlInput(e.target.value)} placeholder="Paste image URL..." style={{flex:1,padding:"0.7rem",borderRadius:"10px",border:"1px solid #1e1c26",background:"#0d0c13",color:"#e8d5b7",outline:"none",fontFamily:"'DM Sans',sans-serif",fontSize:"0.8rem"}}/>
            <button onClick={updateHeroUrl} style={{background:"#c9a96e",border:"none",borderRadius:"10px",padding:"0.7rem 1rem",cursor:"pointer",fontWeight:600,color:"#0d0b10",fontSize:"0.8rem"}}>Apply</button>
          </div>
        </div>
        <div style={{borderRadius:"12px",overflow:"hidden",border:"1px solid #1e1c26",background:"#0c0b11"}}>
          <img src={heroImageUrl} style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
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
function AdminBookings({bookings,setBookings,therapists}){
  const [search,setSearch]=useState("");
  const [statusF,setStatusF]=useState("all");
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const SI2={background:"#0d0c13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"};
  const filtered=useMemo(()=>bookings.filter(b=>(statusF==="all"||b.status===statusF)&&(b.client.toLowerCase().includes(search.toLowerCase())||b.ref.toLowerCase().includes(search.toLowerCase())||b.service.toLowerCase().includes(search.toLowerCase()))).sort((a,b)=>a.date.localeCompare(b.date)||a.time.localeCompare(b.time)),[bookings,search,statusF]);
  const save=(fd)=>{if(editing)setBookings(bb=>bb.map(b=>b.id===editing.id?{...b,...fd}:b));else setBookings(bb=>[...bb,{...fd,id:genId(),ref:genRef(),source:"admin"}]);setShowForm(false);setEditing(null);};
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
            <thead><tr style={{borderBottom:"1px solid #16141f",background:"rgba(255,255,255,0.02)"}}>{["Src","Ref","Client","Service","Therapist","Date/Time","Amount","Status","Payment","Actions"].map(h=><th key={h} style={{padding:"0.62rem 0.8rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.6rem",textTransform:"uppercase",letterSpacing:"0.1em",whiteSpace:"nowrap"}}>{h}</th>)}</tr></thead>
            <tbody>
              {filtered.length===0?<tr><td colSpan={10} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No bookings match</td></tr>:filtered.map((b,i)=>(
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
                  <td style={{padding:"0.55rem 0.8rem"}}>
                    <div style={{display:"flex",gap:"0.25rem",flexWrap:"wrap"}}>
                      {b.status==="pending"&&<button onClick={()=>setBookings(bb=>bb.map(x=>x.id===b.id?{...x,status:"confirmed"}:x))} style={{padding:"0.14rem 0.38rem",borderRadius:"5px",border:"1px solid rgba(92,219,149,0.25)",background:"transparent",color:"#5cdb95",cursor:"pointer",fontSize:"0.58rem"}}>✓</button>}
                      {b.payment==="unpaid"&&b.status!=="cancelled"&&<button onClick={()=>setBookings(bb=>bb.map(x=>x.id===b.id?{...x,payment:"paid"}:x))} style={{padding:"0.14rem 0.38rem",borderRadius:"5px",border:"1px solid rgba(201,169,110,0.25)",background:"transparent",color:"#c9a96e",cursor:"pointer",fontSize:"0.58rem"}}>$</button>}
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
      {showForm&&<BookingFormModal booking={editing} therapists={therapists} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="booking" name={`${deleting.ref} — ${deleting.client}`} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

// Therapists CRUD
function AdminTherapists({therapists,setTherapists,bookings}){
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const save=(fd)=>{if(editing)setTherapists(tt=>tt.map(t=>t.id===editing.id?{...t,...fd}:t));else setTherapists(tt=>[...tt,{...fd,id:genId(),sessions:0,rating:5.0,revenue:0,active:true}]);setShowForm(false);setEditing(null);};
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
  const total=bookings.reduce((s,b)=>s+(b.amount||0),0);
  const CT=({active,payload,label})=>!active||!payload?.length?null:(<div style={{background:"#1a1823",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.55rem 0.8rem",fontSize:"0.74rem"}}><div style={{color:"#5a5060"}}>{label}</div><div style={{color:"#c9a96e",fontWeight:600}}>K{payload[0].value.toLocaleString()}</div></div>);
  const totalRev=bookings.reduce((s,b)=>s+(b.amount||0),0);
  const massageRev=bookings.filter(b=>b.cat==="Massage").reduce((s,b)=>s+(b.amount||0),0);
  const bodyRev=bookings.filter(b=>b.cat==="Body Treatments").reduce((s,b)=>s+(b.amount||0),0);
  const facialRev=bookings.filter(b=>b.cat==="Facials").reduce((s,b)=>s+(b.amount||0),0);
  const nailsRev=bookings.filter(b=>b.cat==="Nails").reduce((s,b)=>s+(b.amount||0),0);
  const lashesRev=bookings.filter(b=>b.cat==="Lashes").reduce((s,b)=>s+(b.amount||0),0);
  const maxCat=Math.max(massageRev,bodyRev,facialRev,nailsRev,lashesRev,1);
  const cats=[{n:"Massage",rev:massageRev,c:"#c9a96e"},{n:"Body Treatments",rev:bodyRev,c:"#5cdb95"},{n:"Facials",rev:facialRev,c:"#a78bfa"},{n:"Nails",rev:nailsRev,c:"#f472b6"},{n:"Lashes",rev:lashesRev,c:"#60a5fa"}];
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%",display:"flex",flexDirection:"column",gap:"1rem"}}>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(135px,1fr))",gap:"0.9rem"}}>
        {[{i:"💰",l:"14-Day Revenue",v:`K${total.toLocaleString()}`,c:"#c9a96e"},{i:"📅",l:"Total Bookings",v:bookings.length,c:"#8b9ef7"},{i:"✅",l:"Completed",v:bookings.filter(b=>b.status==="completed").length,c:"#5cdb95"},{i:"📱",l:"Via App",v:bookings.filter(b=>b.source==="client").length,c:"#a78bfa"}].map(c=>(
          <div key={c.l} style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"12px",padding:"0.95rem",position:"relative",overflow:"hidden"}}>
            <div style={{position:"absolute",top:0,left:0,right:0,height:"2px",background:`linear-gradient(90deg,${c.c}55,transparent)`}}/>
            <div style={{fontSize:"1rem",marginBottom:"0.25rem"}}>{c.i}</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.35rem",color:c.c,fontWeight:600}}>{c.v}</div>
            <div style={{fontSize:"0.6rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em"}}>{c.l}</div>
          </div>
        ))}
      </div>
      <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
        <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Daily Revenue — May 2026</div>
        <ResponsiveContainer width="100%" height={170}>
          <BarChart data={REVENUE_DATA} barSize={14}>
            <CartesianGrid strokeDasharray="3 3" stroke="#16141f" vertical={false}/>
            <XAxis dataKey="day" tick={{fill:"#3a3650",fontSize:8}} axisLine={false} tickLine={false}/>
            <YAxis tick={{fill:"#3a3650",fontSize:9}} axisLine={false} tickLine={false} width={40} tickFormatter={v=>`K${(v/1000).toFixed(0)}k`}/>
            <Tooltip content={<CT/>}/>
            <Bar dataKey="rev" radius={[4,4,0,0]}>{REVENUE_DATA.map((_,i)=><Cell key={i} fill={i===REVENUE_DATA.length-1?"#c9a96e":"#1e1c26"}/>)}</Bar>
          </BarChart>
        </ResponsiveContainer>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"1rem"}}>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>By Category</div>
          {cats.map(c=><div key={c.n} style={{marginBottom:"0.8rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#5a5060"}}>{c.n}</span><span style={{color:c.c,fontWeight:600}}>K{c.rev.toLocaleString()}</span></div><div style={{background:"#0d0c13",borderRadius:"4px",height:"6px"}}><div style={{width:`${Math.round((c.rev/maxCat)*100)}%`,height:"100%",background:`linear-gradient(90deg,${c.c}77,${c.c})`,borderRadius:"4px"}}/></div></div>)}
        </div>
        <div style={{background:"#0f0d14",border:"1px solid #1e1c26",borderRadius:"14px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Payment Status</div>
          {(()=>{const paid=bookings.filter(b=>b.payment==="paid").reduce((s,b)=>s+(b.amount||0),0);const unpaid=bookings.filter(b=>b.payment==="unpaid"&&b.status!=="cancelled").reduce((s,b)=>s+(b.amount||0),0);return[{l:"Collected",v:paid,c:"#5cdb95"},{l:"Outstanding",v:unpaid,c:"#ef4444"}].map(r=><div key={r.l} style={{marginBottom:"0.85rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#5a5060"}}>{r.l}</span><span style={{color:r.c,fontWeight:600}}>K{r.v.toLocaleString()}</span></div><div style={{background:"#0d0c13",borderRadius:"4px",height:"6px"}}><div style={{width:`${Math.round((r.v/(paid+unpaid||1))*100)}%`,height:"100%",background:`linear-gradient(90deg,${r.c}77,${r.c})`,borderRadius:"4px"}}/></div></div>);})()}
        </div>
      </div>
    </div>
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

function AdminApp({
  bookings = [],
  setBookings = () => {},
  therapists = [],
  setTherapists = () => {},
  notifications = [],
  setNotifications = () => {},
  newBookingsCount = 0,
  heroImageUrl,
  setHeroImageUrl
}) {
  const [view, setView] = useState("dashboard");
  const [collapsed, setCollapsed] = useState(false);
  const [showNotifs, setShowNotifs] = useState(false);

  // ✅ SAFE FALLBACKS (prevents crashes)
  const safeBookings = Array.isArray(bookings) ? bookings : [];
  const safeNotifications = Array.isArray(notifications) ? notifications : [];

  const unread = safeNotifications.filter(n => !n.read).length;
  const pending = safeBookings.filter(b => b.status === "pending").length;

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
        />

        <div style={{ flex: 1, display: "flex", flexDirection: "column", overflow: "visible" }}>
          <AdminTopBar
            view={view}
            unread={unread}
            onBell={() => setShowNotifs(s => !s)}
            newBookingsCount={newBookingsCount}
          />

          <div style={{ flex: 1, overflowY: "auto" }}>
            {view === "dashboard" && (
              <AdminDashboard
                bookings={safeBookings}
                notifications={safeNotifications}
                onMarkRead={() =>
                  setNotifications(ns =>
                    (Array.isArray(ns) ? ns : []).map(n => ({ ...n, read: true }))
                  )
                }
                heroImageUrl={heroImageUrl}
                setHeroImageUrl={setHeroImageUrl}
              />
            )}

            {view === "bookings" && (
              <AdminBookings
                bookings={safeBookings}
                setBookings={setBookings}
                therapists={therapists}
              />
            )}

            {view === "therapists" && (
              <AdminTherapists
                therapists={therapists}
                setTherapists={setTherapists}
                bookings={safeBookings}
              />
            )}

            {view === "clients" && <AdminClients bookings={safeBookings} />}

            {view === "revenue" && <AdminRevenue bookings={safeBookings} />}
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

// ═══════════════════════════════════════════════════════════════════════════
//  ROOT — SHARED STATE BRIDGE
// ═══════════════════════════════════════════════════════════════════════════
export default function NgalulaUnifiedApp(){
  const [mode,setMode]=useState("client");
  const [bookings,setBookings]=useState(SEED_BOOKINGS);
  const [therapists,setTherapists]=useState(SEED_THERAPISTS);
  const [heroImageUrl,setHeroImageUrl]=useState("https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1600&q=80");
  const [notifications,setNotifications]=useState([
    {id:1,type:"booking",  msg:"New booking — Natasha Phiri · Couples' Massage",time:"13:00",read:false},
    {id:2,type:"payment",  msg:"Payment received — Yvonne Musonda · K1,100",    time:"12:30",read:false},
    {id:3,type:"booking",  msg:"New booking — Mutale Kabwe · Deep Cleanse Facial",time:"11:00",read:true},
  ]);
  const [newBookingsCount,setNewBookingsCount]=useState(0);
  const [pulseAdmin,setPulseAdmin]=useState(false);
  const [secretClicks,setSecretClicks]=useState(0);
  const [lastSecretClick,setLastSecretClick]=useState(Date.now());
  const [showAdminToast,setShowAdminToast]=useState(false);
  const [toastMessage,setToastMessage]=useState("");

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
    const message = secretClicks < 5 ? `Hidden access ${secretClicks}/5` : (mode === "client" ? "Admin access unlocked" : "Returned to client booking");
    setToastMessage(message);
    setShowAdminToast(true);
    if(secretClicks >= 5){
      setMode(m=>m==="client"?"admin":"client");
      setSecretClicks(0);
    }
    const timer = setTimeout(()=>setShowAdminToast(false),2000);
    return ()=>clearTimeout(timer);
  },[secretClicks,mode]);

  useEffect(()=>{
    const onKeyDown = (e) => {
      if(e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'a'){
        setMode(m=>m==="client"?"admin":"client");
        setToastMessage(prev=>prev.includes("Admin")?"Returned to client booking":"Admin access unlocked");
        setShowAdminToast(true);
        const timer = setTimeout(()=>setShowAdminToast(false),2000);
        return ()=>clearTimeout(timer);
      }
    };
    window.addEventListener('keydown', onKeyDown);
    return ()=>window.removeEventListener('keydown', onKeyDown);
  },[]);

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
      <div style={{height:"100%",paddingTop:"50px",boxSizing:"border-box"}}>
        {mode==="client" && <ClientApp bookings={bookings} therapists={therapists} onNewBooking={handleNewBooking} heroImageUrl={heroImageUrl}/>}
        {mode==="admin" && <AdminApp bookings={bookings} setBookings={setBookings} therapists={therapists} setTherapists={setTherapists} notifications={notifications} setNotifications={setNotifications} newBookingsCount={newBookingsCount} heroImageUrl={heroImageUrl} setHeroImageUrl={setHeroImageUrl}/>}
      </div>
    </div>
  );
}
