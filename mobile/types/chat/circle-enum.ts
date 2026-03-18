export class CircleEnum {
  // Circle Types
  public static readonly TYPE_PRIVATE_CIRCLE = 'private_circle';
  public static readonly TYPE_COMMUNITY_HUB = 'community_hub';

  // Style Codes
  public static readonly STYLE_SAGE = 'sage';
  public static readonly STYLE_STONE = 'stone';
  public static readonly STYLE_CLAY = 'clay';
  public static readonly STYLE_SAND = 'sand';
  public static readonly STYLE_DUSK = 'dusk';
  public static readonly STYLE_AMBER = 'amber';

  // Get all circle types
  public static getTypes(): string[] {
    return [
      this.TYPE_PRIVATE_CIRCLE,
      this.TYPE_COMMUNITY_HUB,
    ];
  }

  // Get all style codes
  public static getStyleCodes(): string[] {
    return [
      this.STYLE_SAGE,
      this.STYLE_STONE,
      this.STYLE_CLAY,
      this.STYLE_SAND,
      this.STYLE_DUSK,
      this.STYLE_AMBER,
    ];
  }
}