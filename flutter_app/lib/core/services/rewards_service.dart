import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:get_storage/get_storage.dart';
import '../config/app_config.dart';
import '../repositories/reward_repository.dart';
import '../models/reward_tier.dart';
import '../models/reward_points.dart';
import '../models/reward_redemption.dart';
import '../models/loyalty_program.dart';
import '../models/achievement.dart';

class RewardsService {
  static final RewardsService _instance = RewardsService._internal();
  factory RewardsService() => _instance;
  RewardsService._internal();

  final RewardRepository _repository = RewardRepository();
  final GetStorage _storage = GetStorage();
  
  LoyaltyProgram? _loyaltyProgram;
  RewardPoints? _userPoints;
  List<RewardTier> _tiers = [];
  List<RewardRedemption> _redemptions = [];
  List<Achievement> _achievements = [];
  StreamController<RewardPoints>? _pointsStreamController;
  
  // Getters
  LoyaltyProgram? get loyaltyProgram => _loyaltyProgram;
  RewardPoints? get userPoints => _userPoints;
  List<RewardTier> get tiers => _tiers;
  List<RewardRedemption> get redemptions => _redemptions;
  List<Achievement> get achievements => _achievements;
  Stream<RewardPoints> get pointsStream => _pointsStreamController?.stream ?? const Stream.empty();

  // Initialize rewards service
  static Future<void> initialize() async {
    final service = RewardsService();
    await service._loadRewardsData();
    service._initializePointsStream();
  }

  Future<void> _loadRewardsData() async {
    try {
      // Load loyalty program
      _loyaltyProgram = await _repository.getLoyaltyProgram();
      
      // Load user points
      _userPoints = await _repository.getUserPoints();
      
      // Load reward tiers
      _tiers = await _repository.getRewardTiers();
      
      // Load user redemptions
      _redemptions = await _repository.getUserRedemptions();
      
      // Load user achievements
      _achievements = await _repository.getUserAchievements();
      
      // Cache data
      await _cacheRewardsData();
    } catch (e) {
      print('Failed to load rewards data: $e');
    }
  }

  void _initializePointsStream() {
    _pointsStreamController = StreamController<RewardPoints>.broadcast();
  }

  Future<void> _cacheRewardsData() async {
    await _storage.write('loyalty_program', _loyaltyProgram?.toJson());
    await _storage.write('user_points', _userPoints?.toJson());
    await _storage.write('reward_tiers', _tiers.map((t) => t.toJson()).toList());
    await _storage.write('redemptions', _redemptions.map((r) => r.toJson()).toList());
    await _storage.write('achievements', _achievements.map((a) => a.toJson()).toList());
  }

  // Earn points from booking
  Future<RewardUpdate> earnBookingPoints(String bookingId, double amount) async {
    try {
      // Calculate points based on amount and tier multiplier
      final basePoints = (amount * 10).round(); // 10 points per dollar
      final tierMultiplier = _getTierMultiplier();
      final earnedPoints = (basePoints * tierMultiplier).round();
      
      // Update user points
      final updatedPoints = await _repository.addPoints(earnedPoints, 'booking', bookingId);
      _userPoints = updatedPoints;
      
      // Check for tier upgrades
      await _checkTierUpgrade();
      
      // Check for achievements
      await _checkAchievements();
      
      // Update stream
      _pointsStreamController?.add(updatedPoints);
      
      // Cache updated data
      await _cacheRewardsData();
      
      return RewardUpdate(
        pointsEarned: earnedPoints,
        totalPoints: updatedPoints.totalPoints,
        tier: updatedPoints.currentTier,
        tierProgress: updatedPoints.tierProgress,
        nextTierPoints: _getNextTierPoints(),
        achievementsUnlocked: _getNewAchievements(),
      );
    } catch (e) {
      throw RewardsException('Failed to earn booking points: $e');
    }
  }

  // Earn points from review
  Future<RewardUpdate> earnReviewPoints(String bookingId) async {
    try {
      const reviewPoints = 50; // Fixed points for review
      final tierMultiplier = _getTierMultiplier();
      final earnedPoints = (reviewPoints * tierMultiplier).round();
      
      final updatedPoints = await _repository.addPoints(earnedPoints, 'review', bookingId);
      _userPoints = updatedPoints;
      
      await _checkTierUpgrade();
      await _checkAchievements();
      
      _pointsStreamController?.add(updatedPoints);
      await _cacheRewardsData();
      
      return RewardUpdate(
        pointsEarned: earnedPoints,
        totalPoints: updatedPoints.totalPoints,
        tier: updatedPoints.currentTier,
        tierProgress: updatedPoints.tierProgress,
        nextTierPoints: _getNextTierPoints(),
        achievementsUnlocked: _getNewAchievements(),
      );
    } catch (e) {
      throw RewardsException('Failed to earn review points: $e');
    }
  }

  // Earn points from referral
  Future<RewardUpdate> earnReferralPoints(String referralCode) async {
    try {
      const referralPoints = 100; // Fixed points for referral
      final tierMultiplier = _getTierMultiplier();
      final earnedPoints = (referralPoints * tierMultiplier).round();
      
      final updatedPoints = await _repository.addPoints(earnedPoints, 'referral', referralCode);
      _userPoints = updatedPoints;
      
      await _checkTierUpgrade();
      await _checkAchievements();
      
      _pointsStreamController?.add(updatedPoints);
      await _cacheRewardsData();
      
      return RewardUpdate(
        pointsEarned: earnedPoints,
        totalPoints: updatedPoints.totalPoints,
        tier: updatedPoints.currentTier,
        tierProgress: updatedPoints.tierProgress,
        nextTierPoints: _getNextTierPoints(),
        achievementsUnlocked: _getNewAchievements(),
      );
    } catch (e) {
      throw RewardsException('Failed to earn referral points: $e');
    }
  }

  // Earn points from birthday
  Future<RewardUpdate> earnBirthdayPoints() async {
    try {
      const birthdayPoints = 200; // Fixed points for birthday
      final tierMultiplier = _getTierMultiplier();
      final earnedPoints = (birthdayPoints * tierMultiplier).round();
      
      final updatedPoints = await _repository.addPoints(earnedPoints, 'birthday', 'birthday_bonus');
      _userPoints = updatedPoints;
      
      await _checkTierUpgrade();
      await _checkAchievements();
      
      _pointsStreamController?.add(updatedPoints);
      await _cacheRewardsData();
      
      return RewardUpdate(
        pointsEarned: earnedPoints,
        totalPoints: updatedPoints.totalPoints,
        tier: updatedPoints.currentTier,
        tierProgress: updatedPoints.tierProgress,
        nextTierPoints: _getNextTierPoints(),
        achievementsUnlocked: _getNewAchievements(),
      );
    } catch (e) {
      throw RewardsException('Failed to earn birthday points: $e');
    }
  }

  // Redeem reward
  Future<RewardRedemption> redeemReward(String rewardId) async {
    try {
      if (_userPoints == null) {
        throw RewardsException('User points not loaded');
      }
      
      // Get reward details
      final reward = await _repository.getReward(rewardId);
      
      // Check if user has enough points
      if (_userPoints!.availablePoints < reward.pointsRequired) {
        throw RewardsException('Insufficient points');
      }
      
      // Check if reward is available
      if (!reward.isAvailable) {
        throw RewardsException('Reward not available');
      }
      
      // Redeem reward
      final redemption = await _repository.redeemReward(rewardId);
      _redemptions.insert(0, redemption);
      
      // Update user points
      final updatedPoints = await _repository.deductPoints(reward.pointsRequired, 'redemption', rewardId);
      _userPoints = updatedPoints;
      
      _pointsStreamController?.add(updatedPoints);
      await _cacheRewardsData();
      
      return redemption;
    } catch (e) {
      throw RewardsException('Failed to redeem reward: $e');
    }
  }

  // Get available rewards
  Future<List<Reward>> getAvailableRewards() async {
    try {
      final rewards = await _repository.getAvailableRewards();
      
      // Filter rewards based on user tier
      if (_userPoints != null) {
        return rewards.where((reward) => 
          reward.requiredTier <= _getTierLevel(_userPoints!.currentTier)
        ).toList();
      }
      
      return rewards;
    } catch (e) {
      throw RewardsException('Failed to get available rewards: $e');
    }
  }

  // Get tier benefits
  List<String> getTierBenefits() {
    if (_userPoints == null) return [];
    
    final currentTier = _tiers.firstWhere(
      (tier) => tier.name == _userPoints!.currentTier,
      orElse: () => _tiers.first,
    );
    
    return currentTier.benefits;
  }

  // Get points history
  Future<List<PointsTransaction>> getPointsHistory() async {
    try {
      return await _repository.getPointsHistory();
    } catch (e) {
      throw RewardsException('Failed to get points history: $e');
    }
  }

  // Check if user can redeem reward
  bool canRedeemReward(Reward reward) {
    if (_userPoints == null) return false;
    
    return _userPoints!.availablePoints >= reward.pointsRequired &&
           reward.isAvailable &&
           reward.requiredTier <= _getTierLevel(_userPoints!.currentTier);
  }

  // Get points needed for next tier
  int getPointsNeededForNextTier() {
    if (_userPoints == null || _tiers.isEmpty) return 0;
    
    final currentTierIndex = _tiers.indexWhere(
      (tier) => tier.name == _userPoints!.currentTier,
    );
    
    if (currentTierIndex < _tiers.length - 1) {
      return _tiers[currentTierIndex + 1].pointsRequired - _userPoints!.totalPoints;
    }
    
    return 0;
  }

  // Get tier progress percentage
  double getTierProgress() {
    if (_userPoints == null || _tiers.isEmpty) return 1.0;
    
    final currentTierIndex = _tiers.indexWhere(
      (tier) => tier.name == _userPoints!.currentTier,
    );
    
    if (currentTierIndex >= _tiers.length - 1) return 1.0;
    
    final currentTierPoints = _tiers[currentTierIndex].pointsRequired;
    final nextTierPoints = _tiers[currentTierIndex + 1].pointsRequired;
    final userPoints = _userPoints!.totalPoints;
    
    return (userPoints - currentTierPoints) / (nextTierPoints - currentTierPoints);
  }

  // Helper methods
  double _getTierMultiplier() {
    if (_userPoints == null || _tiers.isEmpty) return 1.0;
    
    final currentTier = _tiers.firstWhere(
      (tier) => tier.name == _userPoints!.currentTier,
      orElse: () => _tiers.first,
    );
    
    return currentTier.pointsMultiplier;
  }

  int _getTierLevel(String tierName) {
    final index = _tiers.indexWhere((tier) => tier.name == tierName);
    return index >= 0 ? index : 0;
  }

  int _getNextTierPoints() {
    if (_userPoints == null || _tiers.isEmpty) return 0;
    
    final currentTierIndex = _tiers.indexWhere(
      (tier) => tier.name == _userPoints!.currentTier,
    );
    
    if (currentTierIndex < _tiers.length - 1) {
      return _tiers[currentTierIndex + 1].pointsRequired;
    }
    
    return _tiers[currentTierIndex].pointsRequired;
  }

  List<Achievement> _getNewAchievements() {
    // This would be implemented to check for newly unlocked achievements
    return [];
  }

  Future<void> _checkTierUpgrade() async {
    if (_userPoints == null || _tiers.isEmpty) return;
    
    final userPoints = _userPoints!.totalPoints;
    String newTier = _userPoints!.currentTier;
    
    // Check if user qualifies for a higher tier
    for (final tier in _tiers) {
      if (userPoints >= tier.pointsRequired) {
        newTier = tier.name;
      } else {
        break;
      }
    }
    
    // Update tier if changed
    if (newTier != _userPoints!.currentTier) {
      await _repository.updateUserTier(newTier);
      _userPoints = _userPoints!.copyWith(currentTier: newTier);
    }
  }

  Future<void> _checkAchievements() async {
    if (_userPoints == null) return;
    
    // Check various achievements
    await _checkBookingAchievements();
    await _checkPointsAchievements();
    await _checkRedemptionAchievements();
    await _checkReferralAchievements();
  }

  Future<void> _checkBookingAchievements() async {
    final bookingCount = await _repository.getUserBookingCount();
    
    // First booking achievement
    if (bookingCount == 1) {
      await _unlockAchievement('first_booking');
    }
    
    // Multiple bookings achievements
    if (bookingCount >= 5) {
      await _unlockAchievement('regular_customer');
    }
    
    if (bookingCount >= 10) {
      await _unlockAchievement('loyal_customer');
    }
    
    if (bookingCount >= 25) {
      await _unlockAchievement('vip_customer');
    }
  }

  Future<void> _checkPointsAchievements() async {
    final totalPoints = _userPoints!.totalPoints;
    
    // Points milestones
    if (totalPoints >= 100) {
      await _unlockAchievement('points_100');
    }
    
    if (totalPoints >= 500) {
      await _unlockAchievement('points_500');
    }
    
    if (totalPoints >= 1000) {
      await _unlockAchievement('points_1000');
    }
    
    if (totalPoints >= 5000) {
      await _unlockAchievement('points_5000');
    }
  }

  Future<void> _checkRedemptionAchievements() async {
    final redemptionCount = _redemptions.length;
    
    // First redemption
    if (redemptionCount == 1) {
      await _unlockAchievement('first_redemption');
    }
    
    // Multiple redemptions
    if (redemptionCount >= 5) {
      await _unlockAchievement('reward_collector');
    }
  }

  Future<void> _checkReferralAchievements() async {
    final referralCount = await _repository.getUserReferralCount();
    
    // First referral
    if (referralCount == 1) {
      await _unlockAchievement('first_referral');
    }
    
    // Multiple referrals
    if (referralCount >= 5) {
      await _unlockAchievement('referral_champion');
    }
  }

  Future<void> _unlockAchievement(String achievementId) async {
    try {
      // Check if already unlocked
      if (_achievements.any((a) => a.id == achievementId)) {
        return;
      }
      
      // Unlock achievement
      final achievement = await _repository.unlockAchievement(achievementId);
      _achievements.insert(0, achievement);
      
      // Award bonus points for achievement
      final bonusPoints = achievement.bonusPoints;
      if (bonusPoints > 0) {
        await _repository.addPoints(bonusPoints, 'achievement', achievementId);
      }
      
      await _cacheRewardsData();
    } catch (e) {
      print('Failed to unlock achievement $achievementId: $e');
    }
  }

  // Generate referral code
  Future<String> generateReferralCode() async {
    try {
      final referralCode = await _repository.generateReferralCode();
      return referralCode;
    } catch (e) {
      throw RewardsException('Failed to generate referral code: $e');
    }
  }

  // Get referral stats
  Future<ReferralStats> getReferralStats() async {
    try {
      return await _repository.getReferralStats();
    } catch (e) {
      throw RewardsException('Failed to get referral stats: $e');
    }
  }

  // Dispose
  void dispose() {
    _pointsStreamController?.close();
  }
}

// Supporting models
class RewardUpdate {
  final int pointsEarned;
  final int totalPoints;
  final String tier;
  final double tierProgress;
  final int nextTierPoints;
  final List<Achievement> achievementsUnlocked;
  
  RewardUpdate({
    required this.pointsEarned,
    required this.totalPoints,
    required this.tier,
    required this.tierProgress,
    required this.nextTierPoints,
    required this.achievementsUnlocked,
  });
}

class Reward {
  final String id;
  final String name;
  final String description;
  final int pointsRequired;
  final String requiredTier;
  final bool isAvailable;
  final String imageUrl;
  final String category;
  final DateTime? expiresAt;
  
  Reward({
    required this.id,
    required this.name,
    required this.description,
    required this.pointsRequired,
    required this.requiredTier,
    required this.isAvailable,
    required this.imageUrl,
    required this.category,
    this.expiresAt,
  });
}

class PointsTransaction {
  final String id;
  final int points;
  final String type;
  final String description;
  final DateTime createdAt;
  final String? relatedId;
  
  PointsTransaction({
    required this.id,
    required this.points,
    required this.type,
    required this.description,
    required this.createdAt,
    this.relatedId,
  });
}

class ReferralStats {
  final int totalReferrals;
  final int successfulReferrals;
  final int pointsEarned;
  final String referralCode;
  
  ReferralStats({
    required this.totalReferrals,
    required this.successfulReferrals,
    required this.pointsEarned,
    required this.referralCode,
  });
}

// Exception class
class RewardsException implements Exception {
  final String message;
  RewardsException(this.message);
  
  @override
  String toString() => 'RewardsException: $message';
}
